from bs4 import BeautifulSoup
import sys
import urllib2

f = open('bad_zips.txt', 'a')

zipC = sys.argv[1]
r  = urllib2.urlopen('http://www.melissadata.com/lookups/MapZipV.asp?zip='+zipC)
data = r.read()

soup = BeautifulSoup(data, "lxml")
a = soup.find('font')
if "PO Boxes only!" in a.text:
    f.write(zipC+ " ")
f.close