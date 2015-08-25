SELECT alarm_schedule.scheduleid, alarm_schedule.clusterid, alarm_schedule.activate_time, alarm_schedule.brightness, day_of_week.day AS day_of_week
FROM alarm_schedule
INNER JOIN day_of_week
ON alarm_schedule.day_of_week=day_of_week.dow_id
WHERE alarm_schedule.clusterid = 2
ORDER BY day_of_week.dow_id ASC, alarm_schedule.activate_time ASC;