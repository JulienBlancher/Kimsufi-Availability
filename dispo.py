#!/usr/bin/env python

import json
import pprint
import datetime
import requests
import dateutil.relativedelta

DOMAIN   = 'https://ws.ovh.com/dedicated/r2/ws.dispatcher/'
URL      = 'getAvailability2?callback=Request.JSONP.request_map.request_0'

def get_url_time(ref):
   return DOMAIN+'getElapsedTimeSinceLastDelivery?callback=Request.JSONP.request_map.request_1&params={"gamme":"'+str(ref)+'"}'

raw_content = requests.get(DOMAIN+URL).content

data = json.loads(raw_content.split('(')[1].split(')')[0])

for bloc in range(0,len(data['answer']['availability'])):
   reference = data['answer']['availability'][bloc]['reference']
   time_content = requests.get(get_url_time(reference)).content
   try:
      time_json = json.loads(time_content.split('(')[1].split(')')[0])
      if time_json['error'] != 'null':
         if time_json['answer']:
            last  = dateutil.relativedelta.relativedelta(seconds=int(time_json['answer']))
            now   = datetime.datetime.now()
            print reference+' '+str(now-last)
   except ValueError,e:
      print reference,str(e)