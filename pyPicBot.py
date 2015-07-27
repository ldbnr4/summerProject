from lxml import html
import requests

def getPic( name ):
    f = open('pic.txt', 'w')
    page = requests.get('http://www.last.fm/music/'+name)
    if page.status_code is not 200:
        #print 'pics/concert.jpg'
        f.write('pics/concert.jpg')
        return
    tree = html.fromstring(page.text)
    rows = tree.xpath('//img[@itemprop="image"]/@src')
    if len(rows) is 0:
        #print 'pics/concert.jpg'
        f.write('pics/concert.jpg')
    for row in rows:
        #print row
        f.write('pics/concert.jpg')
        f.closed