from lxml import html
import requests
import urllib2, HTMLParser
import datetime
from dateutil.relativedelta import relativedelta

class MyHTMLParser(HTMLParser.HTMLParser):
	def handle_starttag(self, tag, attrs):
		print "Starting tag:", tag, attrs
	def handle_data(self, data):
		print "Got data:", data
	def handle_endtag(self, tag):
		print "Ending tag:", tag

def getEvents( zipCode ):
    sDate = datetime.date.today().strftime("%m/%d/%Y")
    eDate =  datetime.date.today() + relativedelta(months=3)
    eDate = eDate.strftime("%m/%d/%Y")
    #p = MyHTMLParser()
    #p.feed(urllib2.urlopen('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&Zip='+zipCode+'&radius=50&StartDate='+sDate+'&EndDate='+eDate+'&Rec=False&pagenum=1&pasi=1500').read())
    #print myhtml
    #return
    
    page = requests.get('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&Zip='+zipCode+'&radius=50&StartDate='+sDate+'&EndDate='+eDate+'&Rec=False&pagenum=1&pasi=1500')
    if page.status_code is not 200:
        print 'NULL'
        return
    
    tree = html.fromstring(page.text)
    valids = tree.xpath('//span[@id = "ctl00_MainContent_ctlByDay_PagingControlTop_lblTotalShows"]/text()')
    rows = tree.xpath('//tr[@*]')
    allEs = []
    for row in rows:
        if row.attrib.get('class') == 'dateRow':
            date = row.find('td').find('a').text
        for tag in row.findall('td'):
            if tag.get('class') == 'artistCol':
                events = [None] * 7
                events[0] = '|'
                events[2]= ';'
                events[1] = ';' + date
                i=1;
                length = len(tag.findall('a'))
                for artist in tag.findall('a'):
                    if artist.text:     
                        if i != length:
                            events[2] = events[2] + artist.text + ':'
                        else:
                            events[2] = events[2] + artist.text
                        i = i+1
                    #print events[2]
            elif tag.get('class') == 'venueCol':
                events[3] = ';' + tag.find('a').text
                #print tag.find('a').text
            elif tag.get('class') == 'locationCol':
                i=0
                for location in tag.findall('a'):
                    if i is 0:
                        events[4] = ';' + location.text
                        i=i+1
                    elif i is 1:
                         events[5] = ';' + location.text
            elif tag.get('class') == 'toolCol':
                link = False
                for tickets in tag.findall('a'):
                    if tickets.get('target') == 'buy':
                        link = True
                        events[6] = ';'+tickets.get('href')
                if link is False:
                    events[6] = '; NULL'
                allEs = allEs + events
    print allEs
    return