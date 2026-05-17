const express = require('express');
const sql = require('mssql/msnodesqlv8');
const bcrypt = require('bcrypt');
const cors = require('cors');
const path = require('path');

const app = express();
app.use(express.json());
app.use(cors());
app.use(express.static(path.join(__dirname)));

const dbConfig = {
  driver: 'msnodesqlv8',
  options: {
    connectionString: 'Driver={ODBC Driver 18 for SQL Server};Server=localhost\\SQLEXPRESS;Database=locker_system;Trusted_Connection=Yes;'
  }
};

sql.connect(dbConfig)
  .then(pool => {
    console.log('Connected to SQL Server');
    return pool.request().query('SELECT name FROM sys.databases');
  })
  .then(result => console.log(result.recordset))
  .catch(err => console.error('SQL Error:', err));



// Example route
app.post('/register', async (req, res) => {
  try {
    const { username, password } = req.body;
    const hashedPassword = await bcrypt.hash(password, 10);

    const pool = await sql.connect(dbConfig);
    await pool.request()
      .input('username', sql.VarChar, username)
      .input('password', sql.VarChar, hashedPassword)
      .query('INSERT INTO users (username, password) VALUES (@username, @password)');

    res.status(200).send('User registered successfully');
  } catch (err) {
    console.error('Registration Error:', err);
    res.status(500).send('Error registering user');
  }
});

app.listen(3000, () => {
  console.log('Server running on port 3000');
});
