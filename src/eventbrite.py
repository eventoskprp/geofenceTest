from urllib2 import Request, urlopen, URLError
import simplejson as json
import time
city = "los angeles"
api_url = "https://www.eventbriteapi.com/v3/events/search/?"
venue_url = "https://www.eventbriteapi.com/v3/venues/"
api_token = "P3T4I4MKKLCNVVOYVTRB"
clean_events_data = []

class eventData(object):
    def to_JSON(self):
        return json.dumps(self, default=lambda o: o.__dict__, 
            sort_keys=True, indent=4)
    def __init__(self,name="none",description="none",event_url="none",start_time="none",end_time="none",logo_url="none",long="null",lat="null"):
        self.name=name
        self.description=description
        self.event_url=event_url
        self.start_time=start_time
        self.end_time=end_time
        self.logo_url=logo_url
        self.long=long
        self.lat=lat

def encoding(string):
    if(string):
        return string.encode('utf-8')
    else:
        return "null"
    
def checkNull(string):
    if(string):
        return string
    else:
        return "null"
    
    
def collectingDataEventBrite():
        full_data="{"+'"events"'+":["
#        print "---------------------------"
        for ecd in clean_events_data:
            try:
                full_data+=ecd.to_JSON()
                full_data+=","
            except TypeError:
                print "-------------abc--------------"
                
                print ecd.name
                print ecd.description
                print ecd.start_time
                print ecd.logo_url
                print ecd.event_url
                print ecd.long
                print ecd.lat
        
        full_data+="{}]}"
        return full_data
        
#        print full_data
#        print json.loads(full_data)
    


request_url = api_url+"venue.city="+city.replace(" ","+")+"&token="+api_token
#print request_url



try:
	request = Request(request_url)
        response = urlopen(request)
	api_data = response.read()
        json_data = json.loads(api_data)
#	print json_data
        
        try:
            page_count = json_data['pagination']['page_count']
        except:
            page_count = 0
            
        request_url+="&page="
        
        for this_page in range(1,3):
            
#            print request_url+str(this_page)
            if(this_page>0 and this_page%5==0):
                time.sleep(3900)
                
            request = Request(request_url+str(this_page))
            response = urlopen(request)
            api_data = response.read()
            json_data = json.loads(api_data)
#            print json_data
            try:
                page_size = json_data['pagination']['page_size']
            except:
                page_size = 0
            for event in range(1,page_size):
                try:
                    name = encoding(checkNull(json_data['events'][event]['name']['text']))
                except:
                    name = encoding(checkNull("none"))
                name = name.replace("\\",'');
                name = name.replace("'",'');
                name = name.replace("",'');
                try:
                    description = checkNull(json_data['events'][event]['description']['text'])
                except:
                    description = checkNull("null")
                description = description.replace("\\",'');
                description = description.replace("'",'');
                description = description.replace('"','');
                
                try:
                    start_time =  json_data['events'][event]['start']['local']
                except:
                    start_time="null"
                try:
                    end_time =  json_data['events'][event]['end']['local']
                except:
                    end_time = "null"
                try:
                    logo_url =  encoding(checkNull(json_data['events'][event]['logo']['url']))
                except:
                    logo_url =  encoding(checkNull("null"))
                try:
                    event_url =  encoding(checkNull(json_data['events'][event]['url']))
                except:
                    event_url =  encoding(checkNull("null"))    
                long=0
                lat=0
                try : 
                    venue_request_url = venue_url+json_data['events'][event]['venue_id']+"/?token="+api_token
#                    print venue_request_url
                    request = Request(venue_request_url)
                    response = urlopen(request)
                    venue_data = response.read()
                    json_venue_data = json.loads(venue_data)
#                    print json_venue_data
#                    print json_venue_data['address']['address_1']
#                    print json_venue_data['address']['longitude']
#                    print json_venue_data['latitude']
                    try:
                        long=json_venue_data['longitude']
                    except:
                        long=0
                    try:
                        lat=json_venue_data['latitude']
                    except:
                        lat=0
                    
                except:
                    long="null"
                    lat="null"
                
                
#                print event_url
                
                clean_events_data.append(eventData(name,description,event_url,start_time,end_time,logo_url,long,lat))
        
        collectingDataEventBrite()

                
                
            
        

except URLError, e:
    print 'No kittez. Got an error code:', e
    collectingDataEventBrite()
    
    
    
    
