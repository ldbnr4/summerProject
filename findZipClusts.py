from bs4 import BeautifulSoup
import sys
import urllib2

f = open('zip_clusters.txt', 'w')

zipC = sys.argv[1]
r  = urllib2.urlopen('http://www.searchbug.com/tools/zip-radius.aspx?afid=sbug&zipcode='+ zipC +'&radius=60&submit=Search')
data = r.read()

soup = BeautifulSoup(data, "lxml")
list_of_a = soup.find_all('a')
i = 0
for a in list_of_a:
    if a.text.isnumeric():
        if i == 0:
            i = i + 1
        else:
            f.write(a.text+":")
f.close