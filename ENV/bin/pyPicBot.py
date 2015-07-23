from lxml import html
import requests
import urllib

def getPic( name ):
    page = requests.get('http://www.last.fm/music/'+name)
    if page.status_code is not 200:
        print 'pics/concert.jpg'
        return
    tree = html.fromstring(page.text)
    rows = tree.xpath('//img[@itemprop="image"]/@src')
    if len(rows) is 0:
        print 'pics/concert.jpg'
    for row in rows:
        print row