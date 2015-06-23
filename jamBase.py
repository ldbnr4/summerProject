import mechanize
from lxml import html
import requests

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
control.value = '66062'
br.submit()
br.select_form("aspnetForm")
br.submit()
br.select_form("aspnetForm")
control = br.form.find_control("ctl00$MainContent$ddlRadius")
control.value = ['50',]
br.submit()
for link in br.links():
    if link.text == 'Show ALL':
        response = br.follow_link(link)
content = response.get_data()
tree = html.fromstring(content)
rows = tree.xpath('//*')
events = {}
for row in rows:
    if row.tag == 'tr':
        if row.attrib.get('class') == 'dateRow':
            events['Date'] = row.find('td').find('a').text
            #print row.find('td').find('a').text
        for tag in row.findall('td'):
            if tag.get('class') == 'artistCol':
                for artist in tag.findall('a'):
                    
                    #print artist.text
            #if tag.get('class') == 'venueCol':
                #print tag.find('a').text
            #if tag.get('class') == 'locationCol':
                #for location in tag.findall('a'):
                   # print location.text
        
    #if row.attrib['class'] == ' '
    
#print 'Rows: ', eventInfo