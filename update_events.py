import mechanize

br = mechanize.Browser()
br.set_handle_robots(False)   # ignore robots
response = br.open('http://vamonos-vamos.rhcloud.com/update')
print response.read()

