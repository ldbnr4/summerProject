import mechanize
from lxml import html
import requests

page = requests.get("https://ipinfo.io/")
print page.text

br = mechanize.Browser()
br.open("https://ipinfo.io/")
for form in br.forms():
    print "Form name:", form.name
    print form
br.select_form(nr=0)