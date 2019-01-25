import serial
import paho.mqtt.client as mqtt
import threading
import time
from datetime import datetime
import random

# Schnittstelle zum uC
conf_port = '/dev/ttyUSB0'

# Adresse des MQTT-Broker
conf_broker = '10.23.42.10'

# Bezeichnung der Lampen
conf_lights = [ '1', '2', '3', '4', '5', '6' , '7', '8', '9', '10' ]
# Zuordnung Gruppen zu Lampen
conf_groups = { 'main': [ '1', '2', '3', '4' , '5', '6', '7', '8' ], 'workshop': [ '9', '10' ] }
# Bezeichnung der Taster
conf_buttons = [ 'unused', 'workshop', 'main' ]

# Status vorbereiten
states = {}
for light in conf_lights:
	states[light] = "unknown"
for group, members in conf_groups.items():
	states[group] = "unknown"

# Flag fuer Antwort vom Controller
ser_ack = False

def send_message(topic, payload, keep = False):
	mqtt_client.publish(topic, payload, 0, keep)

def dispatch_command(obj, value):
	for light in conf_lights:
		if light == obj:
			set_output(light, value)
			return

	for group, lights in conf_groups.items():
		if group == obj:
			for light in lights:
				dispatch_command(light, value)
			return
 
def command_write(msg):
	global ser_ack

	ser_ack = False
	ser.write(msg)

	# Maximale 2 Sekunden auf Antwort warten
	for x in range(19):
		time.sleep(0.1)
		if ser_ack == True:
			return True

	# Keine Antwort erhalten
	return False

def request_status():
	print "request_status"
	command_write('ollpera')

def set_output(name, value):
	print "set_output %s -> %s" % (name, value)
	index = str(hex(conf_lights.index(name) + 1).upper())[2:]

	if value == 'on':
		out = 'h'
	elif value == 'off':
		out = 'l'
	elif value == 'misc':
		if random.random() < 0.5:
			out = 'h'
		else:
			out = 'l'
	else:
		return

	command_write('ollpew' + index + out)

def state_changed(buf):
	# Kennzeichnungen am Anfang entfernen
	if buf.startswith('states '):
		buf = buf[7:]

	# Flag fuer Aenderungen
	state_changed = False

	# Laenge aus Nachricht und Konfiguration
	state_count = len(buf)
	light_count = len(conf_lights)

	# Einzellampen aktualisieren
	for index in range(min(state_count, light_count)):
		new_state = "unknown"
		if buf[index] == "1":
			new_state = "on"
		if buf[index] == "0":
			new_state = "off"
		
		cur_state = states[conf_lights[index]]
		if cur_state != new_state:
			state_changed = True
		
		states[conf_lights[index]] = new_state

	# Gruppen aktualisieren
	for group, members in conf_groups.items():
		group_state = ''

		for member in members:
			if group_state == '':
				group_state = states[member]

			if states[member] != group_state:
				group_state = 'misc'

		states[group] = group_state

	print states	
	# Status senden
	for item, state in states.items():
		send_message('/room/light/' + item + '/state', state, True)

	now = datetime.now().replace(microsecond=0).isoformat()
	send_message('/room/light/daemon/last', now,True)

def button_pressed(buf):
	# Kennzeichnung am Anfang entfernen
	if buf.startswith('button'):
		buf = buf[6:]

	index = int(buf[0])
	button = conf_buttons[index]

	send_message('/room/button/' + button, 'toggled')

def uc_rebooted():
	send_message('/room/light/daemon/state', 'uc-reboot')	
	
	# Aktive Lampen merken
	on_lights = []
	for light in conf_lights:
		if states[light] == 'on':
			on_lights.append(light)

	# Lampenstatus abfragen
	request_status()

	# Gemerkten Status wieder setzen
	time.sleep(2)
	for light in on_lights:
		set_output(light, 'on')

def uc_result():
	global ser_ack
	ser_ack = True

def handle_serial_message(msg):
	#print "msg " + msg

	if msg.startswith('state'):
		state_changed(msg)
		return

	if msg.startswith('button'):
		button_pressed(msg)
		return
		
	if msg.startswith('Ueberschalter 2.0'):
		uc_rebooted()
		return

	if msg.startswith('ACK') or msg.startswith('NACK'):
		uc_result()
		return

def serial_worker():
	global ser
	ser = serial.Serial('/dev/ttyUSB0', 9600)

	ser_buf = ''
	while True:
		ser_char = ser.read(1)
		if ser_char == '\n':
			handle_serial_message(ser_buf)
			ser_buf = ''
			continue
		
		if (ord(ser_char) < 0x20) or (ord(ser_char) > 0x7E):
			continue

		ser_buf = ser_buf + ser_char


def handle_mqtt_connect(client, userdata, flags, rc):
	client.subscribe('/room/light/+/command')
	send_message('/room/light/daemon/state', 'online', True)


def handle_mqtt_message(client, userdata, msg):
	if msg.retain == True:
		return

	obj = msg.topic.split('/')[3]
	value = msg.payload
	
	dispatch_thread = threading.Thread(None, dispatch_command, 'DispatchThread', (obj, value, ))
	dispatch_thread.start()

def mqtt_worker():
	global mqtt_client
	mqtt_client = mqtt.Client()
	mqtt_client.on_connect = handle_mqtt_connect
	mqtt_client.on_message = handle_mqtt_message
	mqtt_client.will_set('/room/light/daemon/state', 'offline', 0, True)
	
	mqtt_client.connect(conf_broker)
	mqtt_client.loop_forever()



serial_thread = threading.Thread(None, serial_worker, 'SerialWorkerThread')
serial_thread.daemon = True

mqtt_thread = threading.Thread(None, mqtt_worker, 'MQTTWorkerThread')
mqtt_thread.daemon = True

serial_thread.start()
mqtt_thread.start()

# Warten bis die Threads ihre globalen Objekte angelegt haben
while not 'ser' in globals():
	time.sleep(0.1)
while not 'mqtt_client' in globals():
	time.sleep(0.1)

# Hauptschleife starten - Fragt alle 60 Sekunden den Lampenstatus ab
# Dieser wird dann per MQTT gepusht.
print "enter main loop"
while True:
	request_status()
	time.sleep(60)

