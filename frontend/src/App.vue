<template>
  <div id="app">
    <div class="header">
      <h1>
        <i class="fas fa-graduation-cap"></i> Course Mark Management System
      </h1>
    </div>

    <!-- Homepage -->
    <div v-if="!isAuthenticated && !showLogin" class="container">
      <!-- Hero Section -->
      <div class="hero-section">
        <div class="hero-content">
          <h1 class="hero-title">Streamline Your Academic Assessment</h1>
          <p class="hero-subtitle">
            A modern, efficient system for managing course marks, grades, and
            student performance tracking. Designed for lecturers and students to
            collaborate seamlessly.
          </p>
          <div class="hero-actions">
            <button @click="showLogin = true" class="btn btn-primary">
              <i class="fas fa-sign-in-alt"></i>
              Get Started
            </button>
            <button @click="scrollToFeatures" class="btn btn-secondary">
              <i class="fas fa-info-circle"></i>
              Learn More
            </button>
          </div>
        </div>
        <div class="hero-image">
          <i class="fas fa-chart-line"></i>
        </div>
      </div>

      <!-- Features Section -->
      <div id="features" class="features-section">
        <h2 class="section-title">Why Choose Our System?</h2>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-user-graduate"></i>
            </div>
            <h3>Student Management</h3>
            <p>
              Easily add and manage student records with a clean, intuitive
              interface.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-calculator"></i>
            </div>
            <h3>Automatic Grading</h3>
            <p>
              Automatically calculate final grades based on assignment and exam
              scores.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Performance Analytics</h3>
            <p>
              View comprehensive reports and analytics on student performance.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Secure Access</h3>
            <p>
              Role-based authentication ensures data security and proper access
              control.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <h3>Responsive Design</h3>
            <p>
              Access your data from any device with our mobile-friendly
              interface.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-clock"></i>
            </div>
            <h3>Real-time Updates</h3>
            <p>
              Instant updates and real-time data synchronization across all
              users.
            </p>
          </div>
        </div>
      </div>

      <!-- How It Works Section -->
      <div class="how-it-works-section">
        <h2 class="section-title">How It Works</h2>
        <div class="steps-container">
          <div class="step">
            <div class="step-number">1</div>
            <h3>Login</h3>
            <p>Access the system with your lecturer or student credentials.</p>
          </div>
          <div class="step">
            <div class="step-number">2</div>
            <h3>Manage</h3>
            <p>Add students, enter marks, and track performance metrics.</p>
          </div>
          <div class="step">
            <div class="step-number">3</div>
            <h3>Analyze</h3>
            <p>View comprehensive reports and grade distributions.</p>
          </div>
        </div>
      </div>

      <!-- CTA Section -->
      <div class="cta-section">
        <div class="cta-content">
          <h2>Ready to Get Started?</h2>
          <p>
            Join thousands of educators who trust our system for their academic
            assessment needs.
          </p>
          <button @click="showLogin = true" class="btn btn-primary btn-large">
            <i class="fas fa-rocket"></i>
            Start Now
          </button>
        </div>
      </div>
    </div>

    <!-- Login Form -->
    <div v-if="!isAuthenticated && showLogin" class="container">
      <div class="login-container">
        <div class="login-header">
          <button @click="showLogin = false" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Home
          </button>
        </div>
        <div class="card">
          <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
          <form @submit.prevent="login">
            <div class="form-group">
              <label for="username">Username</label>
              <input
                type="text"
                id="username"
                v-model="loginForm.username"
                placeholder="Enter your username"
                required
              />
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                v-model="loginForm.password"
                placeholder="Enter your password"
                required
              />
            </div>

            <div class="form-group">
              <label for="role">Role</label>
              <select id="role" v-model="loginForm.role" required>
                <option value="">Select Role</option>
                <option value="lecturer">Lecturer</option>
                <option value="student">Student</option>
              </select>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn" :disabled="isLoading">
                <span v-if="isLoading" class="loading"></span>
                <i v-else class="fas fa-sign-in-alt"></i>
                {{ isLoading ? "Logging in..." : "Login" }}
              </button>
            </div>
          </form>

          <div v-if="error" class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            {{ error }}
          </div>
        </div>
      </div>
    </div>

    <!-- Authenticated Dashboard -->
    <div v-else-if="isAuthenticated" class="container">
      <div class="nav">
        <div class="nav-left">
          <a
            href="#"
            @click.prevent="switchToDashboard"
            :class="{ active: currentView === 'dashboard' }"
          >
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
          </a>
          <a
            v-if="user.role === 'lecturer'"
            href="#"
            @click.prevent="switchToAddStudent"
            :class="{ active: currentView === 'addStudent' }"
          >
            <i class="fas fa-user-plus"></i>
            Add Student
          </a>
          <a
            v-if="user.role === 'lecturer'"
            href="#"
            @click.prevent="switchToAddMarks"
            :class="{ active: currentView === 'addMarks' }"
          >
            <i class="fas fa-plus-circle"></i>
            Add Marks
          </a>
          <a
            href="#"
            @click.prevent="switchToViewMarks"
            :class="{ active: currentView === 'viewMarks' }"
          >
            <i class="fas fa-chart-bar"></i>
            View Marks
          </a>
        </div>
        <a href="#" @click.prevent="logout" class="btn btn-danger">
          <i class="fas fa-sign-out-alt"></i>
          Logout
        </a>
      </div>

      <!-- Lecturer Dashboard -->
      <div
        v-if="currentView === 'dashboard' && user.role === 'lecturer'"
        class="card"
      >
        <div class="welcome-section">
          <h2>Welcome back, {{ user.name }}!</h2>
          <p>
            You are logged in as a
            <span class="role-badge">{{ user.role }}</span>
          </p>
        </div>

        <div class="dashboard-stats">
          <div class="stat-card">
            <h3>{{ students.length }}</h3>
            <p>Total Students</p>
          </div>
          <div class="stat-card">
            <h3>{{ marks.length }}</h3>
            <p>Marks Entered</p>
          </div>
          <div class="stat-card">
            <h3>{{ averageMark }}</h3>
            <p>Average Mark</p>
          </div>
        </div>

        <div class="form-actions">
          <button @click="switchToViewMarks" class="btn btn-success">
            <i class="fas fa-eye"></i>
            View All Marks
          </button>
          <button @click="switchToAddMarks" class="btn">
            <i class="fas fa-plus"></i>
            Add New Marks
          </button>
        </div>
      </div>

      <!-- Student Dashboard -->
      <div
        v-if="currentView === 'dashboard' && user.role === 'student'"
        class="card"
      >
        <div class="welcome-section">
          <h2>Welcome back, {{ user.name }}!</h2>
          <p>
            You are logged in as a
            <span class="role-badge">{{ user.role }}</span>
          </p>
        </div>

        <div v-if="marks.length > 0" class="student-marks-overview">
          <h3><i class="fas fa-chart-line"></i> Your Academic Performance</h3>

          <div class="student-marks-card">
            <div class="mark-detail">
              <div class="mark-label">Assignment Mark</div>
              <div class="mark-value">
                {{ parseFloat(marks[0].assignment).toFixed(2) }}/100
              </div>
              <div class="mark-percentage">
                ({{ (parseFloat(marks[0].assignment) * 0.3).toFixed(2) }}% of
                total)
              </div>
            </div>

            <div class="mark-detail">
              <div class="mark-label">Exam Mark</div>
              <div class="mark-value">
                {{ parseFloat(marks[0].exam).toFixed(2) }}/100
              </div>
              <div class="mark-percentage">
                ({{ (parseFloat(marks[0].exam) * 0.7).toFixed(2) }}% of total)
              </div>
            </div>

            <div class="mark-detail total-mark">
              <div class="mark-label">Final Grade</div>
              <div class="mark-value">
                {{ parseFloat(marks[0].total).toFixed(2) }}/100
              </div>
              <div class="mark-grade" :class="getGradeClass(marks[0].total)">
                {{ getGrade(marks[0].total) }}
              </div>
            </div>
          </div>

          <div class="grade-explanation">
            <h4>Grade Scale:</h4>
            <div class="grade-scale">
              <span class="grade-item grade-a">A+ (90-100)</span>
              <span class="grade-item grade-a">A (80-89)</span>
              <span class="grade-item grade-b">B (70-79)</span>
              <span class="grade-item grade-c">C (60-69)</span>
              <span class="grade-item grade-d">D (50-59)</span>
              <span class="grade-item grade-f">F (0-49)</span>
            </div>
          </div>
        </div>

        <div v-else class="no-marks-message">
          <i class="fas fa-clock"></i>
          <h3>No marks available yet</h3>
          <p>
            Your marks will appear here once your lecturer has entered them.
          </p>
        </div>

        <div class="form-actions">
          <button @click="switchToViewMarks" class="btn btn-success">
            <i class="fas fa-eye"></i>
            View My Marks
          </button>
        </div>
      </div>

      <!-- Add Student (Lecturer Only) -->
      <div
        v-if="currentView === 'addStudent' && user.role === 'lecturer'"
        class="card"
      >
        <h2><i class="fas fa-user-plus"></i> Add New Student</h2>
        <form @submit.prevent="addStudent">
          <div class="form-group">
            <label for="studentName">Student Name</label>
            <input
              type="text"
              id="studentName"
              v-model="studentForm.name"
              placeholder="Enter student's full name"
              @input="validateStudentName"
              :class="{ error: studentNameError }"
              required
            />
            <div v-if="studentNameError" class="error-message">
              {{ studentNameError }}
            </div>
          </div>

          <div class="form-group">
            <label for="matricNumber">Matric Number</label>
            <input
              type="text"
              id="matricNumber"
              v-model="studentForm.matric_number"
              placeholder="Enter student's matric number"
              @input="validateMatricNumber"
              :class="{ error: matricNumberError }"
              required
            />
            <div v-if="matricNumberError" class="error-message">
              {{ matricNumberError }}
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-success" :disabled="isLoading">
              <span v-if="isLoading" class="loading"></span>
              <i v-else class="fas fa-user-plus"></i>
              {{ isLoading ? "Adding..." : "Add Student" }}
            </button>
            <button
              type="button"
              @click="switchToDashboard"
              class="btn btn-secondary"
            >
              <i class="fas fa-arrow-left"></i>
              Back to Dashboard
            </button>
          </div>
        </form>

        <div v-if="message" class="alert" :class="messageType">
          <i
            :class="
              messageType === 'alert-success'
                ? 'fas fa-check-circle'
                : 'fas fa-exclamation-triangle'
            "
          ></i>
          {{ message }}
        </div>
      </div>

      <!-- Add Marks (Lecturer Only) -->
      <div
        v-if="currentView === 'addMarks' && user.role === 'lecturer'"
        class="card"
      >
        <h2><i class="fas fa-plus-circle"></i> Add Marks</h2>
        <form @submit.prevent="addMarks">
          <div class="form-group">
            <label for="studentSelect">Select Student</label>
            <select
              id="studentSelect"
              v-model="markForm.student_id"
              required
              :disabled="students.length === 0"
            >
              <option hidden value="">
                {{
                  students.length === 0
                    ? "No students available for marks"
                    : "Select Student"
                }}
              </option>
              <option
                v-for="student in students"
                :key="student.id"
                :value="student.id"
              >
                {{ student.name }} ({{ student.matric_number }})
              </option>
            </select>
            <div v-if="students.length === 0" class="info-message">
              <i class="fas fa-info-circle"></i>
              All students already have marks assigned.
            </div>
          </div>

          <div class="form-group">
            <label for="assignment">Assignment Mark (30%)</label>
            <input
              type="number"
              id="assignment"
              v-model="markForm.assignment"
              min="0"
              max="100"
              step="0.01"
              placeholder="Enter assignment mark"
              required
            />
          </div>

          <div class="form-group">
            <label for="exam">Exam Mark (70%)</label>
            <input
              type="number"
              id="exam"
              v-model="markForm.exam"
              min="0"
              max="100"
              step="0.01"
              placeholder="Enter exam mark"
              required
            />
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-success" :disabled="isLoading">
              <span v-if="isLoading" class="loading"></span>
              <i v-else class="fas fa-plus-circle"></i>
              {{ isLoading ? "Adding..." : "Add Marks" }}
            </button>
            <button
              type="button"
              @click="switchToDashboard"
              class="btn btn-secondary"
            >
              <i class="fas fa-arrow-left"></i>
              Back to Dashboard
            </button>
          </div>
        </form>

        <div v-if="message" class="alert" :class="messageType">
          <i
            :class="
              messageType === 'alert-success'
                ? 'fas fa-check-circle'
                : 'fas fa-exclamation-triangle'
            "
          ></i>
          {{ message }}
        </div>
      </div>

      <!-- View Marks -->
      <div v-if="currentView === 'viewMarks'" class="card">
        <h2><i class="fas fa-chart-bar"></i> Marks Overview</h2>

        <div class="form-actions">
          <button @click="refreshMarks" class="btn" :disabled="isLoading">
            <span v-if="isLoading" class="loading"></span>
            <i v-else class="fas fa-sync-alt"></i>
            {{ isLoading ? "Loading..." : "Refresh Marks" }}
          </button>
          <button @click="switchToDashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
          </button>
        </div>

        <div v-if="marks.length > 0">
          <table class="table">
            <thead>
              <tr>
                <th><i class="fas fa-user"></i> Student Name</th>
                <th v-if="user.role === 'lecturer'">
                  <i class="fas fa-id-card"></i> Matric Number
                </th>
                <th><i class="fas fa-file-alt"></i> Assignment (30%)</th>
                <th><i class="fas fa-file-text"></i> Exam (70%)</th>
                <th><i class="fas fa-calculator"></i> Total</th>
                <th><i class="fas fa-star"></i> Grade</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="mark in marks" :key="mark.name">
                <td>
                  <strong>{{ mark.name }}</strong>
                </td>
                <td v-if="user.role === 'lecturer'">
                  <code>{{ mark.matric_number }}</code>
                </td>
                <td>{{ mark.assignment }}</td>
                <td>{{ mark.exam }}</td>
                <td>
                  <strong>{{ mark.total }}</strong>
                </td>
                <td>
                  <span :class="getGradeClass(mark.total)">
                    {{ getGrade(mark.total) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="empty-state">
          <i class="fas fa-chart-line"></i>
          <h3>No marks found</h3>
          <p>Start by adding some marks to see them here.</p>
          <button
            v-if="user.role === 'lecturer'"
            @click="switchToAddMarks"
            class="btn btn-success"
          >
            <i class="fas fa-plus"></i>
            Add First Mark
          </button>
        </div>

        <div v-if="message" class="alert" :class="messageType">
          <i
            :class="
              messageType === 'alert-success'
                ? 'fas fa-check-circle'
                : 'fas fa-exclamation-triangle'
            "
          ></i>
          {{ message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "App",
  data() {
    return {
      isAuthenticated: false,
      user: null,
      currentView: "dashboard",
      isLoading: false,
      loginForm: {
        username: "",
        password: "",
        role: "",
      },
      studentForm: {
        name: "",
        matric_number: "",
      },
      markForm: {
        student_id: "",
        assignment: "",
        exam: "",
      },
      students: [],
      marks: [],
      error: "",
      message: "",
      messageType: "alert-success",
      showLogin: false, // Added for homepage visibility
      studentNameError: "",
      matricNumberError: "",
    };
  },
  computed: {
    averageMark() {
      if (this.marks.length === 0) return "N/A";
      const total = this.marks.reduce(
        (sum, mark) => sum + parseFloat(mark.total),
        0
      );
      return (total / this.marks.length).toFixed(1);
    },
  },
  async mounted() {
    await this.checkAuth();
    if (this.isAuthenticated) {
      await this.loadInitialData();
    }
  },
  methods: {
    async login() {
      this.isLoading = true;
      this.error = "";
      try {
        const response = await axios.post("/api/login", this.loginForm);
        if (response.data.success) {
          this.user = response.data.user;
          this.isAuthenticated = true;
          this.currentView = "dashboard";

          // Load initial data for dashboard
          await this.loadInitialData();
        }
      } catch (error) {
        this.error =
          error.response?.data?.error ||
          "Login failed. Please check your credentials.";
      } finally {
        this.isLoading = false;
      }
    },

    async logout() {
      try {
        await axios.post("/api/logout");
        this.isAuthenticated = false;
        this.user = null;
        this.currentView = "dashboard";
        this.error = "";
        this.message = "";
        this.students = [];
        this.marks = [];
      } catch (error) {
        console.error("Logout error:", error);
      }
    },

    async checkAuth() {
      try {
        const response = await axios.get("/api/check-auth");
        if (response.data.user) {
          this.user = response.data.user;
          this.isAuthenticated = true;
          // Load initial data for dashboard
          await this.loadInitialData();
        }
      } catch (error) {
        // Not authenticated, stay on login page
      }
    },

    async addStudent() {
      // Clear previous errors
      this.clearStudentFormErrors();

      // Validate inputs
      this.validateStudentName();
      this.validateMatricNumber();

      // Check if there are any validation errors
      if (this.studentNameError || this.matricNumberError) {
        this.message = "Please fix the validation errors before submitting.";
        this.messageType = "alert-error";
        return;
      }

      this.isLoading = true;
      this.message = "";
      try {
        const response = await axios.post("/api/add-student", this.studentForm);
        if (response.data.success) {
          this.message = response.data.message || "Student added successfully!";
          this.messageType = "alert-success";
          this.studentForm.name = "";
          this.studentForm.matric_number = ""; // Clear matric number after adding
          await this.loadStudents();
        }
      } catch (error) {
        this.message =
          error.response?.data?.error ||
          "Failed to add student. Please try again.";
        this.messageType = "alert-error";
      } finally {
        this.isLoading = false;
      }
    },

    async addMarks() {
      this.isLoading = true;
      this.message = "";
      try {
        const response = await axios.post("/api/add-marks", this.markForm);
        if (response.data.success) {
          this.message = `Marks added successfully! Total: ${response.data.total}`;
          this.messageType = "alert-success";
          this.markForm = {
            student_id: "",
            assignment: "",
            exam: "",
          };
          // Refresh students list to remove the student who just got marks
          if (this.user.role === "lecturer") {
            await this.loadStudentsWithoutMarks();
          }
        }
      } catch (error) {
        this.message =
          error.response?.data?.error ||
          "Failed to add marks. Please try again.";
        this.messageType = "alert-error";
      } finally {
        this.isLoading = false;
      }
    },

    async loadInitialData() {
      try {
        // Load marks for dashboard statistics
        await this.loadMarks();

        // Load students if user is lecturer
        if (this.user.role === "lecturer") {
          await this.loadStudents();
        }
      } catch (error) {
        console.error("Failed to load initial data:", error);
      }
    },

    async loadStudents() {
      try {
        const response = await axios.get("/api/get-students");
        if (response.data.success) {
          this.students = response.data.students;
        }
      } catch (error) {
        console.error("Failed to load students:", error);
      }
    },

    async loadStudentsWithoutMarks() {
      try {
        const response = await axios.get("/api/get-students-without-marks");
        if (response.data.success) {
          this.students = response.data.students;
        }
      } catch (error) {
        console.error("Failed to load students without marks:", error);
      }
    },

    async loadMarks(showMessage = false) {
      this.isLoading = true;
      if (showMessage) {
        this.message = "";
      }
      try {
        const response = await axios.get("/api/get-marks");
        if (response.data.success) {
          this.marks = response.data.marks;
          if (showMessage) {
            this.message = "Marks loaded successfully!";
            this.messageType = "alert-success";
          }
        }
      } catch (error) {
        if (showMessage) {
          this.message =
            error.response?.data?.error ||
            "Failed to load marks. Please try again.";
          this.messageType = "alert-error";
        }
      } finally {
        this.isLoading = false;
      }
    },

    getGrade(total) {
      const num = parseFloat(total);
      if (num >= 90) return "A+";
      if (num >= 80) return "A";
      if (num >= 70) return "B";
      if (num >= 60) return "C";
      if (num >= 50) return "D";
      return "F";
    },

    getGradeClass(total) {
      const num = parseFloat(total);
      if (num >= 80) return "grade-a";
      if (num >= 70) return "grade-b";
      if (num >= 60) return "grade-c";
      if (num >= 50) return "grade-d";
      return "grade-f";
    },

    scrollToFeatures() {
      const featuresSection = document.getElementById("features");
      if (featuresSection) {
        featuresSection.scrollIntoView({ behavior: "smooth" });
      }
    },

    validateStudentName() {
      const name = this.studentForm.name.trim();

      if (name.length === 0) {
        this.studentNameError = "";
        return;
      }

      if (name.length < 3) {
        this.studentNameError =
          "Student name must be at least 3 characters long";
        return;
      }

      if (name.length > 100) {
        this.studentNameError = "Student name must be less than 100 characters";
        return;
      }

      if (!/^[a-zA-Z\s\'-]+$/.test(name)) {
        this.studentNameError = "Student name can only contain letters";
        return;
      }

      this.studentNameError = "";
    },

    validateMatricNumber() {
      const matricNumber = this.studentForm.matric_number.trim();

      if (matricNumber.length === 0) {
        this.matricNumberError = "";
        return;
      }

      if (matricNumber.length < 3) {
        this.matricNumberError =
          "Matric number must be at least 3 characters long";
        return;
      }

      if (matricNumber.length > 20) {
        this.matricNumberError =
          "Matric number must be less than 20 characters";
        return;
      }

      if (!/^[a-zA-Z0-9\-_\.]+$/.test(matricNumber)) {
        this.matricNumberError =
          "Matric number can only contain letters, numbers, hyphens, underscores, and dots";
        return;
      }

      this.matricNumberError = "";
    },

    clearStudentFormErrors() {
      this.studentNameError = "";
      this.matricNumberError = "";
    },

    // Navigation methods that clear messages
    async switchToDashboard() {
      this.currentView = "dashboard";
      this.message = "";
      this.error = "";
      await this.loadInitialData();
    },

    async switchToAddStudent() {
      this.currentView = "addStudent";
      this.message = "";
      this.error = "";
      this.clearStudentFormErrors();
      if (this.user.role === "lecturer") {
        await this.loadStudents();
      }
    },

    async switchToAddMarks() {
      this.currentView = "addMarks";
      this.message = "";
      this.error = "";
      if (this.user.role === "lecturer") {
        await this.loadStudentsWithoutMarks();
      }
    },

    async switchToViewMarks() {
      this.currentView = "viewMarks";
      this.message = "";
      this.error = "";
      await this.loadMarks();
    },

    async refreshMarks() {
      await this.loadMarks(true); // Show success message when manually refreshing
    },
  },
};
</script>

<style scoped>
.grade-a {
  color: #10b981;
  font-weight: 600;
}

.grade-b {
  color: #3b82f6;
  font-weight: 600;
}

.grade-c {
  color: #f59e0b;
  font-weight: 600;
}

.grade-d {
  color: #f97316;
  font-weight: 600;
}

.grade-f {
  color: #ef4444;
  font-weight: 600;
}

/* Matric number styling */
code {
  background: var(--border-light);
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-sm);
  font-family: "Courier New", monospace;
  font-size: 0.875rem;
  color: var(--primary-color);
  font-weight: 500;
}

/* Form validation styling */
.form-group input.error {
  border-color: var(--danger-color);
  box-shadow: 0 0 0 3px rgb(239 68 68 / 0.1);
}

.error-message {
  color: var(--danger-color);
  font-size: 0.875rem;
  margin-top: 0.5rem;
  font-weight: 500;
}

.info-message {
  color: var(--text-secondary);
  font-size: 0.875rem;
  margin-top: 0.5rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.info-message i {
  color: var(--primary-color);
}

/* Student Dashboard Styles */
.student-marks-overview {
  margin: 2rem 0;
}

.student-marks-overview h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.student-marks-overview h3 i {
  color: var(--primary-color);
}

.student-marks-card {
  background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
  color: white;
  padding: 2rem;
  border-radius: var(--radius-lg);
  margin-bottom: 2rem;
  box-shadow: var(--shadow-lg);
}

.mark-detail {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 1rem;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.mark-detail:last-child {
  border-bottom: none;
}

.mark-detail.total-mark {
  border-top: 2px solid rgba(255, 255, 255, 0.3);
  margin-top: 1rem;
  padding-top: 1.5rem;
  font-weight: 600;
}

.mark-label {
  font-size: 1rem;
  opacity: 0.9;
  text-align: left;
}

.mark-value {
  font-size: 1.25rem;
  font-weight: 600;
  text-align: center;
  min-width: 120px;
}

.mark-percentage {
  font-size: 0.875rem;
  opacity: 0.8;
  text-align: right;
  min-width: 140px;
}

.mark-grade {
  font-size: 1.5rem;
  font-weight: 700;
  padding: 0.5rem 1rem;
  border-radius: var(--radius-sm);
  background: rgba(255, 255, 255, 0.2);
}

.grade-explanation {
  background: var(--border-light);
  padding: 1.5rem;
  border-radius: var(--radius-md);
  margin-bottom: 2rem;
}

.grade-explanation h4 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.grade-scale {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.grade-item {
  padding: 0.5rem 1rem;
  border-radius: var(--radius-sm);
  font-size: 0.875rem;
  font-weight: 500;
  background: white;
  border: 1px solid var(--border-color);
}

.no-marks-message {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--text-muted);
}

.no-marks-message i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.no-marks-message h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: var(--text-secondary);
}

.no-marks-message p {
  font-size: 1rem;
  line-height: 1.6;
}

/* Table spacing */
.table {
  margin-bottom: 2rem;
}

/* New styles for homepage */
.hero-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 50px 20px;
  background-color: #f0f2f5;
  border-radius: 10px;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.hero-content {
  flex: 1;
  padding-right: 50px;
}

.hero-title {
  font-size: 3.5rem;
  color: #333;
  margin-bottom: 20px;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 30px;
  line-height: 1.6;
}

.hero-actions {
  display: flex;
  gap: 15px;
}

.hero-image {
  flex: 1;
  text-align: center;
  font-size: 5rem; /* Larger icon for hero section */
  color: #4f46e5; /* A blue-ish color for the icon */
}

.features-section {
  padding: 50px 20px;
  background-color: #fff;
  margin-bottom: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  color: #333;
  margin-bottom: 40px;
  position: relative;
}

.section-title::after {
  content: "";
  display: block;
  width: 80px;
  height: 4px;
  background-color: #4f46e5;
  margin: 20px auto;
  border-radius: 2px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
  text-align: center;
}

.feature-card {
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  font-size: 4rem;
  color: #4f46e5;
  margin-bottom: 20px;
}

.feature-card h3 {
  font-size: 1.8rem;
  color: #333;
  margin-bottom: 15px;
}

.feature-card p {
  font-size: 1rem;
  color: #666;
  line-height: 1.6;
}

.how-it-works-section {
  padding: 50px 20px;
  background-color: #f9fafb;
  margin-bottom: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.steps-container {
  display: flex;
  justify-content: space-around;
  gap: 20px;
}

.step {
  flex: 1;
  text-align: center;
  padding: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.step:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.step-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: #4f46e5;
  margin-bottom: 15px;
}

.step h3 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 10px;
}

.step p {
  font-size: 1rem;
  color: #666;
  line-height: 1.5;
}

.cta-section {
  padding: 50px 20px;
  background-color: #4f46e5;
  color: #fff;
  text-align: center;
  border-radius: 10px;
  margin-top: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.cta-content h2 {
  font-size: 2.2rem;
  margin-bottom: 15px;
  color: #fff;
}

.cta-content p {
  font-size: 1.1rem;
  margin-bottom: 30px;
  color: #eee;
}

.btn-large {
  padding: 15px 30px;
  font-size: 1.1rem;
}

.back-btn {
  background-color: #fff;
  color: #333;
  padding: 10px 20px;
  border-radius: 8px;
  border: 1px solid #eee;
  font-size: 0.9rem;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.back-btn:hover {
  background-color: #f0f0f0;
  color: #333;
}

.back-btn i {
  font-size: 1rem;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .hero-section {
    flex-direction: column;
    text-align: center;
    padding: 30px 20px;
  }

  .hero-content {
    padding-right: 0;
  }

  .hero-title {
    font-size: 2.8rem;
  }

  .hero-subtitle {
    font-size: 1rem;
  }

  .hero-actions {
    flex-direction: column;
    gap: 10px;
  }

  .hero-image {
    font-size: 3.5rem;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .steps-container {
    flex-direction: column;
    align-items: center;
  }

  .step {
    width: 80%;
    max-width: 350px;
  }
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 2.2rem;
  }

  .hero-subtitle {
    font-size: 0.9rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .feature-card h3 {
    font-size: 1.5rem;
  }

  .step-number {
    font-size: 2rem;
  }

  .step h3 {
    font-size: 1.2rem;
  }

  .cta-content h2 {
    font-size: 1.8rem;
  }

  .cta-content p {
    font-size: 1rem;
  }

  .btn-large {
    padding: 12px 25px;
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 1.8rem;
  }

  .hero-subtitle {
    font-size: 0.8rem;
  }

  .section-title {
    font-size: 1.8rem;
  }

  .feature-card h3 {
    font-size: 1.3rem;
  }

  .step-number {
    font-size: 1.8rem;
  }

  .step h3 {
    font-size: 1rem;
  }

  .cta-content h2 {
    font-size: 1.5rem;
  }

  .cta-content p {
    font-size: 0.9rem;
  }

  .btn-large {
    padding: 10px 20px;
    font-size: 0.9rem;
  }
}
</style>
