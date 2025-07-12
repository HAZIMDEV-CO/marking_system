# Course Mark Management System

A minimal web application for managing course marks with Vue.js frontend and PHP Slim backend.

## Features

- **Authentication**: Login with role selection (Lecturer/Student)
- **Mark Entry (Lecturer)**: Add students and their marks
- **Mark Viewing (Student)**: View marks in read-only format
- **Backend API**: PHP Slim Framework with MySQL database

## Project Structure

```
marking/
├── frontend/          # Vue.js application
├── backend/           # PHP Slim API
├── database/          # MySQL schema and data
└── README.md
```

## Setup Instructions

1. **Database Setup**:
   - Import `database/schema.sql` to create tables
   - Update database connection in `backend/simple-index.php`

2. **Backend Setup**:
   - No dependencies required (uses built-in PHP features)
   - Start PHP server: `php -S localhost:8000`

3. **Frontend Setup**:
   - Install Node.js dependencies: `npm install`
   - Start development server: `npm run dev`

## API Endpoints

- `POST /login` - User authentication
- `POST /add-student` - Add new student (Lecturer only)
- `POST /add-marks` - Add marks for student (Lecturer only)
- `GET /get-marks` - Get marks (role-based access)

## Budget Breakdown

- Authentication: RM80
- Mark Entry (Lecturer): RM100
- Mark Viewing (Student): RM80
- Backend API & Database: RM40
- **Total: RM300** 