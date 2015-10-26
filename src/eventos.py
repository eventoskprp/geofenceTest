#from eventful import *
from eventbrite import *
from helper import *
import simplejson as json
import psycopg2
import sys
from datetime import datetime





def db_insert(cur,data,con):
#    print data
    for i in range(len(data)-1):
        name  = data[i]['name'].encode('utf-8')[0:199]
#        name = name.replace("\\",'');
#        name = name.replace("'",'');
#        name = name.replace("",'');
        
        
        description  = data[i]['description'].encode('utf-8')[0:199]
#        remove below three lines as its been done in eventful scripts
#        description = description.replace("\\",'');
#        description = description.replace("'",'');
#        description = description.replace('"','');
        event_url  = data[i]['event_url'][0:199]
        logo_url  = data[i]['logo_url'][0:199]
        source = "1"


        if len(data[i]['end_time'])>4:
            end_time = "'"+data[i]['end_time']+"'"
            
        else:
            end_time = data[i]['end_time']

            
         
        if data[i]['start_time'] is not "null":
            start_time = "'"+data[i]['start_time']+"'"
        else:
            start_time = data[i]['start_time']
        
        query = "INSERT INTO Events ( eventName, description, logo_url, end_time, start_time, sourceWeightage, eventWebsiteURL, location) VALUES('"+name+"','"+description+"','"+logo_url+"',"+end_time+","+start_time+",'"+source+"','"+event_url+"',"+"ST_GeographyFromText('SRID=4326;POINT("+data[i]['long']+" "+data[i]['lat']+")'))"
        print query
        cur.execute(query)
        
#        try:
#            query = "INSERT INTO Events ( eventName, description, logo_url, end_time, start_time, sourceWeightage, eventWebsiteURL, location) VALUES('"+name+"','"+description+"','"+logo_url+"',"+end_time+","+start_time+",'"+source+"','"+event_url+"',"+"ST_GeographyFromText('SRID=4326;POINT("+data[i]['long']+" "+data[i]['lat']+")'))"
#            print query
#            cur.execute(query)
#        except DatabaseError,e:
#            print e
#            try:
#                print str(i)+" "+name
#            except:
#                print name
#            try:
#                print str(i)+" "+description
#            except:
#                print description
#            try:
#                print str(i)+" "+event_url
#            except:
#                print event_url
#            try:
#                print str(i)+" "+event_url
#            except:
#                print event_url
#            try:
#                print str(i)+" "+start_time
#            except:
#                print start_time
#            try:
#                print str(i)+" "+end_time
#            except:
#                print end_time
#            try:
#                print str(i)+" "+data[i]['lat']
#            except:
#                print data[i]['lat']
#            try:
#                print str(i)+" "+data[i]['long']
#            except:
#                print data[i]['long']
#            
#            print "--------------------------------------"
            
    con.commit()
    print str(datetime.now())
    print "Records created successfully";
        
    
        

def main():
#    eventDataEF_json = collectingDataEventFul()
    eventDataEB_json = collectingDataEventBrite()

#    eventDataEF = json.loads(eventDataEF_json)
    eventDataEB = json.loads(eventDataEB_json)

#    print eventDataEF['events']
    print eventDataEB['events']


    con = None

    try:

        con = psycopg2.connect(database="eventos", user="eventos", password="12345678", host="eventos.cyeijrrcwqvl.us-west-2.rds.amazonaws.com", port="5590")

        cur = con.cursor()    
#        db_insert(cur,eventDataEF['events'],con)
        db_insert(cur,eventDataEB['events'],con)
#        db_insert(cur,temp_data,con)



    except psycopg2.DatabaseError, e:
        print 'Error %s' % e    
        sys.exit(1)


    finally:

        if con:
            con.close()

if  __name__ =='__main__':main()

#
#

#
#for i in range(len(temp_data)-1):
#    print str(i)+" "+temp_data[i]['name']
#    print str(i)+" "+temp_data[i]['description'].encode('utf-8')
#    print str(i)+" "+temp_data[i]['event_url']
#    print str(i)+" "+temp_data[i]['logo_url']
#    print str(i)+" "+temp_data[i]['start_time']
#    print str(i)+" "+temp_data[i]['end_time']
#    print str(i)+" "+temp_data[i]['lat']
#    print str(i)+" "+temp_data[i]['long']
#    print "--------------------------------------"
#    
#
#
#
