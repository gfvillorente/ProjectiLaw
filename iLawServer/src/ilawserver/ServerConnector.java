package ilawserver;

import java.net.URLConnection;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.ScheduledFuture;
import static java.util.concurrent.TimeUnit.SECONDS;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;


/**
 *
 * @author Kana Antonio
 */
public class ServerConnector {
    public static void main(String[] args){
        DatabaseConnector dc = new DatabaseConnector();
        new BulbInformationChecker(dc);
        new WritePowerAnalysis(dc);
       new SendScheduledLightEvents(dc);
    }
}
/*
 * BulbInformationChecker - Checks and corrects the state, mode and level of each lamp. 
 * Runs after 0 milliseconds on server start up and checks every 50000 milliseconds.
 */
class BulbInformationChecker{
    DatabaseConnector dc;
    BulbInformationChecker(DatabaseConnector dc){
        this.dc = dc;
    }    
    private ArrayList<String> getAllIpAddresses(){
        return dc.getAllIpAddresses();
    }   
    private final ScheduledExecutorService scheduler = Executors.newScheduledThreadPool(1);
    final Runnable bulbinformationchecker = new Runnable(){    
        @Override
        public void run(){
            for(String ip : getAllIpAddresses()){
                try {
                    System.out.println("***BulbInformationChecker()");
                    String result;

                    URL url = new URL("http://" + ip + "/lightvalues.txt");
                    URLConnection conn = url.openConnection();
                    BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream()));
                    String[] str = new String[3];

                    while ((result = br.readLine()) != null) {
                        str = result.split(",");
                    }
                    Lamp lamp = new Lamp(ip, str[0], str[1], str[2]);
                    dc.checkBulbConsistency(lamp);    
                } catch (Exception ex) {
                    dc.changeStateToCnbr(ip);
                    System.out.println("The lamp is currently unreachable. Please check connections for " + ip);
                    System.out.println(ex);
                }
            }
        }
    };
    final ScheduledFuture<?> writer = scheduler.scheduleAtFixedRate(bulbinformationchecker, 1, 10, SECONDS);
}
/*
 * WritePowerAnalysis - Retrieves and writes the power analysis of each lamp.
 * Runs after 250 seconds on server start up and checks every 5 seconds
 * 
 */
class WritePowerAnalysis{
    DatabaseConnector dc;
    WritePowerAnalysis(DatabaseConnector dc){
        this.dc = dc;
    }
    private final ScheduledExecutorService scheduler = Executors.newScheduledThreadPool(1);
    final Runnable writepoweranalysis = new Runnable(){    
        @Override
        public void run(){
          for(String ip: dc.getAllIpAddresses()){
                try {
                    System.out.println("***WritePowerAnalysis()");
                    
                    String urlForReadings = "http://" + ip + "/send.php";
                    URL url = new URL(urlForReadings);
                    URLConnection conn = url.openConnection();
                    BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream()));

                    String inputLine;
                    StringBuilder sb = new StringBuilder();

                    while ((inputLine = br.readLine()) != null) {
                        sb.append(inputLine);
                    }
                    String str_url = sb.toString();

                    JSONParser parser = new JSONParser();
                    Object obj = parser.parse(str_url);
                    JSONArray array = (JSONArray)obj;

                    for(int i = 0; i < array.size(); i++){
                        JSONObject json = (JSONObject)array.get(i);
                        String stat = json.get("stat").toString().trim();
                        String pf = json.get("pf").toString().trim();
                        String watts = json.get("watts").toString().trim();
                        String va = json.get("va").toString().trim();
                        String var = json.get("var").toString().trim();
                        String volt = json.get("volt").toString().trim();
                        String ampere = json.get("ampere").toString().trim();
                        String timestamp = json.get("timestamp").toString().trim();
                        
                        if((stat.equals("InRange"))||(stat.equals("Overflw"))){
                            Readings readings = new Readings(ip, stat, pf, watts, va,
                                var, volt, ampere, timestamp);
                            dc.writePowerAnalysis(readings);
                        }
                    }

                } catch(Exception e) {
                    dc.changeStateToCnbr(ip);
                    System.out.println("The lamp is currently unreachable. Please check connections for " + ip);
                    System.out.println(e);
                }
            }
        }
    };
    final ScheduledFuture<?> writer = scheduler.scheduleAtFixedRate(writepoweranalysis, 11, 10, SECONDS);
}
/*
 * SendScheduledLightEvents - Retrieves and sends lamp schedule to the Raspberry Pi 
 * Runs on the first :30 time interval on server start up and checks every 30 minutes
 */
class SendScheduledLightEvents{
    DatabaseConnector dc;
    Calendar serverStartTime;
    SendScheduledLightEvents(DatabaseConnector dc){
        this.dc = dc;
    }
    private final ScheduledExecutorService scheduler = Executors.newScheduledThreadPool(1);
    private SimpleDateFormat sdf_date = new SimpleDateFormat("yyyy-MM-dd");
    private SimpleDateFormat sdf_time = new SimpleDateFormat("HH:mm:ss");
    private SimpleDateFormat sdf_time_2 = new SimpleDateFormat("HHmm");
    
    private ArrayList<Schedule> schedule_on = new ArrayList<>();
    private ArrayList<Schedule> schedule_off = new ArrayList<>();
    
    final Runnable sendscheduledlightevents = new Runnable(){    
        @Override
        public void run(){
            System.out.println("***SendScheduledLightEvents()"); 
            List<ArrayList<Object>> on;
            List<ArrayList<Object>> off;
            ArrayList<Integer> bulbids = new ArrayList<>();
            Calendar date = Calendar.getInstance();
            date.set(Calendar.SECOND, 0);
            
            Calendar end = Calendar.getInstance();
            //end.add(Calendar.DATE, 1);
            end.set(Calendar.SECOND, 0);
            
            on = dc.getLampSchedule(sdf_date.format(date.getTime()), null, sdf_time.format(date.getTime()), null);
            off = dc.getLampSchedule(null, sdf_date.format(end.getTime()), null, sdf_time.format(end.getTime()));
            System.out.println("on size() - " + on.size());
            System.out.println("off size() - " + off.size());
            for(ArrayList<Object> ar: on){
                int clusterid = (int) ar.get(0);
                int bulbid = (int) ar.get(1);
                int brightness = (int) ar.get(2);
                
                String ipaddress = ar.get(3).toString();
                String name = ar.get(4).toString();
                String start_date = ar.get(5).toString();
                String end_date = ar.get(6).toString();
                String start_time = ar.get(7).toString();
                String end_time = ar.get(8).toString();
                
                bulbids.add(bulbid);
                Schedule sched = new Schedule(ipaddress, bulbid, brightness, start_date, 
                        end_date, start_time, end_time);
                
                sendLightOnSchedule(sched);
                
                //sendLightOffSchedule();
                //schedule_on.add(sched);
            }
            for(ArrayList<Object> ar: off){
                
                int bulbid = (int) ar.get(1);
                if(!bulbids.contains(bulbid)){
                    int clusterid = (int) ar.get(0);
                    int brightness = (int) ar.get(2);

                    String ipaddress = ar.get(3).toString();
                    String name = ar.get(4).toString();
                    String start_date = ar.get(5).toString();
                    String end_date = ar.get(6).toString();
                    String start_time = ar.get(7).toString();
                    String end_time = ar.get(8).toString();

                    Schedule sched = new Schedule(ipaddress, bulbid, brightness, start_date, 
                            end_date, start_time, end_time);

                    sendLightOffSchedule(sched);
                }
            }
        }
    };
    
    private int setTime(){
        serverStartTime = Calendar.getInstance();
        System.out.println("Initial time for lamp scheduling: " + serverStartTime.getTime());
        
        int serverTime = serverStartTime.get(Calendar.MINUTE);
        int checker = serverTime >= 30 ? 60 : 30;
        
        int thirtyMinuteInterval = (checker - serverTime) * 60;
        System.out.println("seconds - " + thirtyMinuteInterval);

        if(serverStartTime.get(Calendar.MINUTE) < 30){
            serverStartTime.set(Calendar.MINUTE, 30);
        } else {
            serverStartTime.set(Calendar.MINUTE, 0);
            serverStartTime.add(Calendar.HOUR_OF_DAY, 1);           
        }
        
        serverStartTime.set(Calendar.SECOND, 0);
        System.out.println("Next scheduled setting of lamps will run at " + serverStartTime.getTime());
        return thirtyMinuteInterval;
    }
    
    final ScheduledFuture<?> writer = scheduler.scheduleAtFixedRate(sendscheduledlightevents, setTime() , 1800000, SECONDS);  
    
    private void sendLightOnSchedule(Schedule schedule){
        try {
            System.out.println("Turning on lights...");
            Calendar currentTime = Calendar.getInstance();
            currentTime.set(Calendar.SECOND, 0);

            String ip = schedule.getIpaddress();
            String state = "on";
            int level = schedule.getBrightness();
            String mode = "control";
            //String url = "http://localhost:81/test3.php?id=" + level;
            String url = "http://" + ip + "/ilawcontrol.php?state=" + state + "&level=" + level + "&mode=" + mode;

            URL obj = new URL(url);

            HttpURLConnection con = (HttpURLConnection) obj.openConnection();
            con.setRequestMethod("GET");

            int responseCode = con.getResponseCode();
            System.out.println("\nSending 'GET' request to URL : " + url);
            System.out.println("Response Code : " + responseCode);

            StringBuilder response;

            try (BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()))) {
                String inputLine;
                response = new StringBuilder();
                while ((inputLine = in.readLine()) != null) {
                        response.append(inputLine);
                }
            }
            System.out.println("Next scheduled setting of lamps will run at " + serverStartTime.getTime());

        } catch (Exception ex) {
            dc.changeStateToCnbr(schedule.getIpaddress());
            System.out.println("Failed to send schedule for lamp " + schedule.getIpaddress() + ". Please check connections.");
        }
    }
    private void sendLightOffSchedule(Schedule schedule){
        try {
            System.out.println("Turning off lights...");
            Calendar currentTime = Calendar.getInstance();
            currentTime.set(Calendar.SECOND, 0);

            String ip = schedule.getIpaddress();
            String state = "off";
            int level = 0;
            String mode = "control";
            //String url = "http://localhost:81/test3.php?id=" + level;
            String url = "http://" + ip + "/ilawcontrol.php?state=" + state + "&level=" + level + "&mode=" + mode;

            URL obj = new URL(url);

            HttpURLConnection con = (HttpURLConnection) obj.openConnection();
            con.setRequestMethod("GET");

            int responseCode = con.getResponseCode();
            System.out.println("\nSending 'GET' request to URL : " + url);

            StringBuilder response;

            try (BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()))) {
                String inputLine;
                response = new StringBuilder();
                while ((inputLine = in.readLine()) != null) {
                        response.append(inputLine);
                }
            }
            System.out.println("Next scheduled setting of lamps will run at " + serverStartTime.getTime());

        } catch (Exception ex) {
            dc.changeStateToCnbr(schedule.getIpaddress());
            System.out.println("Failed to send schedule for lamp " + schedule.getIpaddress() + ". Please check connections.");
        }
    }
}

/*
 * DatabaseConnector - Directly connects to the database
 */
class DatabaseConnector {
    DataSource ds;
    DatabaseConnector(){
        ds = new DataSource();
        this.connectToDatabase();
    }
    protected void connectToDatabase(){
        String url = "jdbc:mysql://localhost:3306/";
        String name = "ilaw";
        String user = "pi";//"root";
        String password = "raspberry";//"";
        ds.connectDB(url, name, user, password);   
    }
    protected ArrayList<String> getAllIpAddresses(){
        return ds.getAllIpAddresses();
    }
    protected void checkBulbConsistency(Lamp lamp){
        String ip = lamp.getIpaddress();
        String state = lamp.getState();
        String level = lamp.getLevel();
        String mode = lamp.getMode();

        System.out.println("Checking possible inconsistencies...");  
        String[] info = ds.getLampInfo(ip);
     
        if(!info[0].equalsIgnoreCase(state)){
            System.out.println("WARNING: INCONSISTENT BULB STATE!");
            System.out.println("\tInfo: Bulb is set to " + state + " while database is set to " + info[0]);
            System.out.println("Correcting inconsistencies...");
            ds.correctBulbState(ip, state);
        } 
        System.out.println("Consistent bulb state...");
        
        if(!info[1].equalsIgnoreCase(level)){
            System.out.println("WARNING: INCONSISTENT BULB BRIGHTNESS LEVEL!");
            System.out.println("\tInfo: Bulb level is set to " + level + " while database is set to " + info[1]);
            System.out.println("Correcting inconsistencies...");
            ds.correctBulbLevel(ip, level);
        } 
        System.out.println("Consistent bulb level...");
        
        if(!info[2].equalsIgnoreCase(mode)){
            System.out.println("WARNING: INCONSISTENT BULB MODE!");
            System.out.println("\tInfo: Bulb mode is set to " + mode + " while database is set to " + info[2]);
            System.out.println("Correcting inconsistencies...");
            ds.correctBulbMode(ip, mode);
        } 
        System.out.println("Consistent bulb mode...");
    }  
    protected void writePowerAnalysis(Readings readings) throws Exception{    
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        
        ds.writePowerAnalysis(readings.getIp(), readings.getStat(), readings.getWatts(), readings.getVa(),
                readings.getVar(), readings.getPf(), readings.getPf(), readings.getAmpere(), sdf.format(sdf.parse(readings.getTimestamp())));
    }
    protected List<ArrayList<Object>> getLampSchedule(String start_date, String end_date, String start_time, String end_time){
        System.out.println("start date - " + start_date);
        System.out.println("end date - " + end_date);
        System.out.println("start time - " + start_time);
        System.out.println("end date - " + end_time);
        
        if(end_date == null && end_time == null){
            return ds.getLampSchedule_on(start_date, start_time);
        } else {
            return ds.getLampSchedule_off(end_date, end_time);
        }
    }
    
    protected void changeStateToCnbr(String ipaddress){
        if(!ipaddress.isEmpty()){
            ds.setUnreachableBulbInfo(ipaddress);
        }
    }
    
    protected void disconnectDatabase(){
        ds.close(null, null, true);
    }
}
