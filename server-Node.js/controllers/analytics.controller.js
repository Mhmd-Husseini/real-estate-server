const connection = require('./../configs/db.connection.js');
const mysql = require("mysql2/promise");

const getAnalyticsData = async (req, res) => {
  try {
    const analyticsData = {};
    const con = await mysql.createConnection(connection.config); 

    const [usersAndAuthors] = await con.execute(
      "SELECT SUM(CASE WHEN role_id = 2 THEN 1 ELSE 0 END) AS users_count, SUM(CASE WHEN role_id = 3 THEN 1 ELSE 0 END) AS authors_count FROM users"
    );
    analyticsData.usersCount = usersAndAuthors[0].users_count;
    analyticsData.authorsCount = usersAndAuthors[0].authors_count;

    const [propertiesCounts] = await con.execute(
      "SELECT DATE_FORMAT(p.created_at, '%Y-%U') AS week, COUNT(*) AS count FROM properties p GROUP BY week ORDER BY week"
    );
    analyticsData.propertiesCounts = propertiesCounts;

    const [usersCounts] = await con.execute(
      "SELECT DATE_FORMAT(u.created_at, '%Y-%U') AS week, COUNT(*) AS count FROM users u GROUP BY week ORDER BY week"
    );
    analyticsData.usersCounts = usersCounts;

    const [landAvgPrices] = await con.execute(
      "SELECT c.city AS city_name, AVG(p.price / (p.area / 100)) AS avg_price_per_100m2 FROM properties p JOIN cities c ON p.city_id = c.id WHERE p.type = 'land' GROUP BY p.city_id"
    );
    analyticsData.landAvgPrices = landAvgPrices;    

    const [homeAvgPrices] = await con.execute(
      "SELECT c.city AS city_name, AVG(p.price / (p.area / 100)) AS avg_price_per_100m2 FROM properties p JOIN cities c ON p.city_id = c.id WHERE p.type = 'home' GROUP BY p.city_id"
    );
    analyticsData.homeAvgPrices = homeAvgPrices;    

    const [meetingCounts] = await con.execute(
      "SELECT DATE_FORMAT(m.date, '%Y-%U') AS week, COUNT(*) AS count FROM meetings m GROUP BY week ORDER BY week"
    );
    analyticsData.meetingCounts = meetingCounts;

    const [propertyCountsByCity] = await con.execute(
      "SELECT c.city AS city_name, COUNT(*) AS count FROM properties p JOIN cities c ON p.city_id = c.id GROUP BY p.city_id"
    );
    analyticsData.propertyCountsByCity = propertyCountsByCity;

    res.json(analyticsData);
  } catch (error) {
    console.error("Error retrieving analytics data:", error);
    res.status(500).json({ error: "Internal Server Error" });
  }
};

module.exports = { getAnalyticsData };
