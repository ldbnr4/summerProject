import mechanize
from lxml import html
import requests

def merge_two_dicts(x, y):
    '''Given two dicts, merge them into a new dict as a shallow copy.'''
    z = x.copy()
    z.update(y)
    return z

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
z = {}
page = {}
i = 0
for row in rows:
    if row.tag == 'tr':
        #print i
        events = {}
        events['Artists']=[]
        events['CityState']=[]
        if row.attrib.get('class') == 'dateRow':
            events['Date'] = row.find('td').find('a').text
            #print row.find('td').find('a').text
        for tag in row.findall('td'):
            if tag.get('class') == 'artistCol':
                for artist in tag.findall('a'):
                    #if i == 0:
                        #events['Artists'] = artist.text
                    #else:
                    events['Artists'].append(artist.text)
                    #i=+1
                    #print artist.text
            if tag.get('class') == 'venueCol':
                events['Venue'] =  tag.find('a').text
                #print tag.find('a').text
            if tag.get('class') == 'locationCol':
                for location in tag.findall('a'):
                    events['CityState'].append(location.text)
                    #print location.text
            print events
    #page = merge_two_dicts(page,events)
        
    #if row.attrib['class'] == ' '
    
#print events