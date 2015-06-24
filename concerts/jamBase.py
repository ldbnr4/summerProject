import mechanize
from lxml import html
import requests
import sys
import json

br = mechanize.Browser()
br.open("http://www.jambase.com/")
'''for form in br.forms():
    print "Form name:", form.name
    print form'''
br.select_form("aspnetForm")
'''for control in br.form.controls:
    print control
    print "type=%s, name=%s value=%s" % (control.type, control.name, br[control.name])'''
control = br.form.find_control("ctl00$MainContent$ctlShowModule$ctlShowFinder$inLocation")
control.value = str(sys.argv[1])
br.submit()
br.select_form("aspnetForm")
br.submit()
br.select_form("aspnetForm")
control = br.form.find_control("ctl00$MainContent$ddlRadius")
control.value = [str(sys.argv[2]),]
br.submit()
for link in br.links():
    if link.text == 'Show ALL':
        response = br.follow_link(link)
content = response.get_data()
tree = html.fromstring(content)
rows = tree.xpath('//*')
page = []
f = open(str(sys.argv[1])+'_'+str(sys.argv[2])+'.txt','w')
for row in rows:
    if row.tag == 'tr':
        #print i
        if row.attrib.get('class') == 'dateRow':
            date = row.find('td').find('a').text
            #print row.find('td').find('a').text
        for tag in row.findall('td'):
            if tag.get('class') == 'artistCol':
                events = {}
                events['Artists']=[]
                events['CityState']=[]
                events['Date'] = date
                for artist in tag.findall('a'):
                    events['Artists'].append(artist.text)
                    #print artist.text
            elif tag.get('class') == 'venueCol':
                events['Venue'] =  tag.find('a').text
                #print tag.find('a').text
            elif tag.get('class') == 'locationCol':
                for location in tag.findall('a'):
                    events['CityState'].append(location.text)
                    #print location.text
                
                f.write(str(events))
                #f.write(str(json.dumps(events,separators=(',', ':','['))))
                #page.append(events)
                #print events
