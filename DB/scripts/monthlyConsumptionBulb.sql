SELECT
	Year(timeinterval) as year, 
	Month(timeinterval) - 1 as month, 
	sum(abs(ave_va * ave_pf)) as total_watts
FROM
	(
	select 
		avg(va) as ave_va, 
		avg(pf) as ave_pf, 
		convert((min(timestamp) div 6000)*6000, datetime) as timeinterval
	from poweranalyzer
	where 
		bulbid = 3 AND 
        YEAR(timestamp) = 2015
	group by timestamp div 6000
	) as newdb
GROUP BY
	Year(timeinterval),
	Month(timeinterval);