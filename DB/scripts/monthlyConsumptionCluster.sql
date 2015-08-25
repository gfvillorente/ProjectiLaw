SELECT
	Year(timeinterval) as year, 
	Month(timeinterval) - 1 as month, 
	sum(ave_va * ave_pf) as total_watts
FROM
	(
		select 
			avg(va) as ave_va, 
			avg(pf) as ave_pf, 
			convert((min(timestamp) div 6000)*6000, datetime) as timeinterval
		from poweranalyzer
		where 
			YEAR(timestamp) = 2015
		AND 
			bulbid IN (
				SELECT DISTINCT bulb.bulbid as bulbid
				FROM bulb
				INNER JOIN cluster_bulb
				ON cluster_bulb.bulbid = bulb.bulbid
				WHERE cluster_bulb.clusterid = 1
			)
		group by timestamp div 6000
	) as newdb
GROUP BY
	Year(timeinterval),
	Month(timeinterval);