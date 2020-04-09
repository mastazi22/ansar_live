import csv
import requests

data = {'from': "8804445650099", 'to': '8801813208359', 'text': 'Hello Inforbip','messageId':''}

headers = {"Content-Type":"application/json", "Accept":"application/json", "Authorization": "Basic U2h1cmpvbXVraGk6UlQ7R2pZLTI2NEFrQEUhLA=="}

r = requests.post("https://api.infobip.com/sms/1/text/single", data=data, headers=headers)
print(r.text)
