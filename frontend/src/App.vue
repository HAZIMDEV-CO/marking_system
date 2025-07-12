<template>
  <div id="app">
    <div class="header">
      <h1>Course Mark Management System</h1>
    </div>

    <!-- Login Form -->
    <div v-if="!isAuthenticated" class="container">
      <div class="card">
        <h2>Login</h2>
        <form @submit.prevent="login">
          <div class="form-group">
            <label for="username">Username:</label>
            <input 
              type="text" 
              id="username" 
              v-model="loginForm.username" 
              required
            />
          </div>
          
          <div class="form-group">
            <label for="password">Password:</label>
            <input 
              type="password" 
              id="password" 
              v-model="loginForm.password" 
              required
            />
          </div>
          
          <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" v-model="loginForm.role" required>
              <option value="">Select Role</option>
              <option value="lecturer">Lecturer</option>
              <option value="student">Student</option>
            </select>
          </div>
          
          <button type="submit" class="btn">Login</button>
        </form>
        
        <div v-if="error" class="alert alert-error">
          {{ error }}
        </div>
      </div>
    </div>

    <!-- Authenticated Dashboard -->
    <div v-else class="container">
      <div class="nav">
        <a href="#" @click.prevent="currentView = 'dashboard'" :class="{ active: currentView === 'dashboard' }">
          Dashboard
        </a>
        <a v-if="user.role === 'lecturer'" href="#" @click.prevent="currentView = 'addStudent'" :class="{ active: currentView === 'addStudent' }">
          Add Student
        </a>
        <a v-if="user.role === 'lecturer'" href="#" @click.prevent="currentView = 'addMarks'" :class="{ active: currentView === 'addMarks' }">
          Add Marks
        </a>
        <a href="#" @click.prevent="currentView = 'viewMarks'" :class="{ active: currentView === 'viewMarks' }">
          View Marks
        </a>
        <a href="#" @click.prevent="logout" class="btn btn-danger" style="float: right;">
          Logout
        </a>
      </div>

      <!-- Dashboard -->
      <div v-if="currentView === 'dashboard'" class="card">
        <h2>Welcome, {{ user.username }}!</h2>
        <p>Role: {{ user.role }}</p>
        <p>You are logged in successfully.</p>
      </div>

      <!-- Add Student (Lecturer Only) -->
      <div v-if="currentView === 'addStudent' && user.role === 'lecturer'" class="card">
        <h2>Add New Student</h2>
        <form @submit.prevent="addStudent">
          <div class="form-group">
            <label for="studentName">Student Name:</label>
            <input 
              type="text" 
              id="studentName" 
              v-model="studentForm.name" 
              required
            />
          </div>
          
          <button type="submit" class="btn">Add Student</button>
        </form>
        
        <div v-if="message" class="alert" :class="messageType">
          {{ message }}
        </div>
      </div>

      <!-- Add Marks (Lecturer Only) -->
      <div v-if="currentView === 'addMarks' && user.role === 'lecturer'" class="card">
        <h2>Add Marks</h2>
        <form @submit.prevent="addMarks">
          <div class="form-group">
            <label for="studentSelect">Select Student:</label>
            <select id="studentSelect" v-model="markForm.student_id" required>
              <option value="">Select Student</option>
              <option v-for="student in students" :key="student.id" :value="student.id">
                {{ student.name }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="assignment">Assignment Mark (30%):</label>
            <input 
              type="number" 
              id="assignment" 
              v-model="markForm.assignment" 
              min="0" 
              max="100" 
              step="0.01"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="exam">Exam Mark (70%):</label>
            <input 
              type="number" 
              id="exam" 
              v-model="markForm.exam" 
              min="0" 
              max="100" 
              step="0.01"
              required
            />
          </div>
          
          <button type="submit" class="btn">Add Marks</button>
        </form>
        
        <div v-if="message" class="alert" :class="messageType">
          {{ message }}
        </div>
      </div>

      <!-- View Marks -->
      <div v-if="currentView === 'viewMarks'" class="card">
        <h2>Marks</h2>
        <button @click="loadMarks" class="btn">Refresh Marks</button>
        
        <table v-if="marks.length > 0" class="table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Assignment (30%)</th>
              <th>Exam (70%)</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="mark in marks" :key="mark.name">
              <td>{{ mark.name }}</td>
              <td>{{ mark.assignment }}</td>
              <td>{{ mark.exam }}</td>
              <td>{{ mark.total }}</td>
            </tr>
          </tbody>
        </table>
        
        <p v-else>No marks found.</p>
        
        <div v-if="message" class="alert" :class="messageType">
          {{ message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'App',
  data() {
    return {
      isAuthenticated: false,
      user: null,
      currentView: 'dashboard',
      loginForm: {
        username: '',
        password: '',
        role: ''
      },
      studentForm: {
        name: ''
      },
      markForm: {
        student_id: '',
        assignment: '',
        exam: ''
      },
      students: [],
      marks: [],
      error: '',
      message: '',
      messageType: 'alert-success'
    }
  },
  async mounted() {
    await this.checkAuth()
    if (this.isAuthenticated && this.user.role === 'lecturer') {
      await this.loadStudents()
    }
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('/api/login', this.loginForm)
        if (response.data.success) {
          this.user = response.data.user
          this.isAuthenticated = true
          this.error = ''
          this.currentView = 'dashboard'
          
          if (this.user.role === 'lecturer') {
            await this.loadStudents()
          }
        }
      } catch (error) {
        this.error = error.response?.data?.error || 'Login failed'
      }
    },
    
    async logout() {
      try {
        await axios.post('/api/logout')
        this.isAuthenticated = false
        this.user = null
        this.currentView = 'dashboard'
        this.error = ''
        this.message = ''
      } catch (error) {
        console.error('Logout error:', error)
      }
    },
    
    async checkAuth() {
      try {
        const response = await axios.get('/api/check-auth')
        if (response.data.user) {
          this.user = response.data.user
          this.isAuthenticated = true
          if (this.user.role === 'lecturer') {
            await this.loadStudents()
          }
        }
      } catch (error) {
        // Not authenticated, stay on login page
      }
    },
    
    async addStudent() {
      try {
        const response = await axios.post('/api/add-student', this.studentForm)
        if (response.data.success) {
          this.message = 'Student added successfully!'
          this.messageType = 'alert-success'
          this.studentForm.name = ''
          await this.loadStudents()
        }
      } catch (error) {
        this.message = error.response?.data?.error || 'Failed to add student'
        this.messageType = 'alert-error'
      }
    },
    
    async addMarks() {
      try {
        const response = await axios.post('/api/add-marks', this.markForm)
        if (response.data.success) {
          this.message = `Marks added successfully! Total: ${response.data.total}`
          this.messageType = 'alert-success'
          this.markForm = {
            student_id: '',
            assignment: '',
            exam: ''
          }
        }
      } catch (error) {
        this.message = error.response?.data?.error || 'Failed to add marks'
        this.messageType = 'alert-error'
      }
    },
    
    async loadStudents() {
      try {
        const response = await axios.get('/api/get-students')
        if (response.data.success) {
          this.students = response.data.students
        }
      } catch (error) {
        console.error('Failed to load students:', error)
      }
    },
    
    async loadMarks() {
      try {
        const response = await axios.get('/api/get-marks')
        if (response.data.success) {
          this.marks = response.data.marks
          this.message = 'Marks loaded successfully!'
          this.messageType = 'alert-success'
        }
      } catch (error) {
        this.message = error.response?.data?.error || 'Failed to load marks'
        this.messageType = 'alert-error'
      }
    }
  }
}
</script> 