# Setup Instructions

## Prerequisites

1. **PHP 7.4+** with PDO MySQL extension
2. **MySQL 5.7+** or MariaDB 10.2+
3. **Node.js 14+** and npm

## Database Setup

1. **Create MySQL Database**:
   ```sql
   -- Run the schema file
   mysql -u root -p < database/schema.sql
   ```

2. **Update Database Configuration** (if needed):
   - Edit `backend/simple-index.php` (lines 10-13)
   - Update host, username, password as needed

## Backend Setup

1. **No PHP Dependencies Required**:
   - The backend uses only built-in PHP features
   - No Composer installation needed

2. **Start PHP Server**:
   ```bash
   cd backend
   php -S localhost:8000
   ```

## Frontend Setup

1. **Install Node.js Dependencies**:
   ```bash
   cd frontend
   npm install
   ```

2. **Start Development Server**:
   ```bash
   cd frontend
   npm run dev
   ```

## Access the Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000

## Test Accounts

### Lecturer Account
- Username: `lecturer1`
- Password: `password`
- Role: `lecturer`

### Student Accounts
- Username: `student1` or `student2`
- Password: `password`
- Role: `student`

## Features

### Lecturer Features
1. **Login** with lecturer credentials
2. **Add Students** - Add new students to the system
3. **Add Marks** - Enter assignment and exam marks
4. **View All Marks** - See marks for all students

### Student Features
1. **Login** with student credentials
2. **View Marks** - See only their own marks (read-only)

## API Endpoints

- `POST /login` - User authentication
- `POST /logout` - User logout
- `GET /check-auth` - Check authentication status
- `POST /add-student` - Add new student (Lecturer only)
- `POST /add-marks` - Add marks for student (Lecturer only)
- `GET /get-marks` - Get marks (role-based access)
- `GET /get-students` - Get all students (Lecturer only)

## Troubleshooting

1. **Database Connection Error**:
   - Check MySQL service is running
   - Verify database credentials in `backend/config/database.php`

2. **CORS Issues**:
   - Ensure backend is running on port 8000
   - Check Vite proxy configuration in `frontend/vite.config.js`

3. **Session Issues**:
   - Ensure PHP sessions are enabled
   - Check file permissions for session storage

## Development Notes

- The application uses PHP sessions for authentication
- Total marks are calculated as: Assignment (30%) + Exam (70%)
- Students can only view their own marks
- Lecturers can view and manage all marks
- No edit/delete functionality as per requirements 