from lxml import html
import requests
import urllib
import datetime
from dateutil.relativedelta import relativedelta

def getEvents( zipCode ):
    sDate = datetime.date.today().strftime("%m/%d/%Y")
    eDate =  datetime.date.today() + relativedelta(months=3)
    eDate = eDate.strftime("%m/%d/%Y")
    
    page = requests.get('http://www.jambase.com/shows/Shows.aspx?ArtistID=0&VenueID=0&Zip='+zipCode+'&radius=50&StartDate='+sDate+'&EndDate='+eDate+'&Rec=False&pagenum=1&pasi=1500')
    if page.status_code is not 200:
        print 'NULL'
    
    tree = html.fromstring(page.text)
    valids = tree.xpath('//span[@id = "ctl00_MainContent_ctlByDay_PagingControlTop_lblTotalShows"]/text()')
    rows = tree.xpath('//tr[@*]')
    allEs = []
    for row in rows:
        if row.attrib.get('class') == 'dateRow':
            date = row.find('td').find('a').text
        for tag in row.findall('td'):
            if tag.get('class') == 'artistCol':
                events = [None] * 6
                events[0] = '|'
                events[2]=[]
                events[1] = date + ';'
                for artist in tag.findall('a'):
                    events[2].append(artist.text + ':')
                    #print artist.text
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
                    #return location.text
                #f.write(str(events))
                allEs = allEs + events
    print allEs