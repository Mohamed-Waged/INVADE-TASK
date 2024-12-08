<template>
    <div class="task-list">
      <h2>Task List</h2>
  
      <!-- Login Form -->
      <div v-if="!isLoggedIn" class="auth-form">
        <h3>Login</h3>
        <form @submit.prevent="login">
          <input type="email" v-model="loginEmail" placeholder="Email" required />
          <input type="password" v-model="loginPassword" placeholder="Password" required />
          <button type="submit">Login</button>
        </form>
  
        <p>Don't have an account? <span @click="showRegisterForm">Register</span></p>
      </div>
  
      <!-- Register Form -->
      <div v-if="showRegister" class="auth-form">
        <h3>Register</h3>
        <form @submit.prevent="register">
          <input type="email" v-model="registerEmail" placeholder="Email" required />
          <input type="password" v-model="registerPassword" placeholder="Password" required />
          <button type="submit">Register</button>
        </form>
  
        <p>Already have an account? <span @click="showLoginForm">Login</span></p>
      </div>
  
      <!-- Task List and Add Task Form -->
      <div v-if="isLoggedIn">
        <div class="task-form">
          <h3>Add New Task</h3>
          <form @submit.prevent="addTask">
            <input type="text" v-model="newTaskTitle" placeholder="Task title" required />
            <button type="submit">Add Task</button>
          </form>
        </div>
  
        <ul>
          <li v-for="task in tasks" :key="task.id" class="task-item">
            <div>
              <input type="checkbox" :checked="task.completed" @change="toggleCompletion(task)" />
              <span :class="{'completed': task.completed}">{{ task.title }}</span>
              <button @click="editTask(task)">Edit</button>
              <button @click="deleteTask(task.id)">Delete</button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        tasks: [],
        newTaskTitle: '',
        loginEmail: '',
        token: '',
        loginPassword: '',
        registerEmail: '',
        registerPassword: '',
        isLoggedIn: false,
        showRegister: false,
      };
    },
    methods: {
      async fetchTasks() {
        if (!this.isLoggedIn) return;
        console.log(`Bearer ${this.token}`)
        try {
          const response = await fetch('http://localhost:8000/api/v1/tasks', {
            headers: { Authorization: `Bearer ${this.token}` }
          });
          if (response.ok) {
            this.tasks = await response.json();
          }
        } catch (error) {
          console.error('Error fetching tasks:', error);
        }
      },
      async addTask() {
        try {
          const response = await fetch('http://localhost:8000/api/v1/tasks', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.token}`,
            },
            body: JSON.stringify({ title: this.newTaskTitle, completed: false }),
          });
          if (response.ok) {
            const newTask = await response.json();
            this.tasks.push(newTask);
            this.newTaskTitle = ''; // Reset input
          }
        } catch (error) {
          console.error('Error adding task:', error);
        }
      },
      async toggleCompletion(task) {
        try {
          const response = await fetch(`http://localhost:8000/api/v1/tasks/${task.id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${this.token}`,
            },
            body: JSON.stringify({ completed: !task.completed }),
          });
          if (response.ok) {
            task.completed = !task.completed;
          }
        } catch (error) {
          console.error('Error toggling task completion:', error);
        }
      },
      async editTask(task) {
        const newTitle = prompt('Edit task title:', task.title);
        if (newTitle) {
          try {
            const response = await fetch(`http://localhost:8000/api/v1/tasks/${task.id}`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${this.token}`,
              },
              body: JSON.stringify({ title: newTitle }),
            });
            if (response.ok) {
              task.title = newTitle;
            }
          } catch (error) {
            console.error('Error editing task:', error);
          }
        }
      },
      async deleteTask(taskId) {
        try {
          const response = await fetch(`http://localhost:8000/api/v1/tasks/${taskId}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${this.token}` },
          });
          if (response.ok) {
            this.tasks = this.tasks.filter(task => task.id !== taskId);
          }
        } catch (error) {
          console.error('Error deleting task:', error);
        }
      },
      async login() {
        try {
          const response = await fetch('http://localhost:8000/api/v1/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: this.loginEmail, password: this.loginPassword }),
          });
          if (response.ok) {
            const data = await response.json();
            this.token = data.accessToken;  // Store the token
            localStorage.setItem('accessToken',data?.data.accessToken)
            this.isLoggedIn = true;
            this.fetchTasks();
          } else {
            console.error('Login failed');
          }

        } catch (error) {
          console.error('Login error:', error);
        }
      },
      async register() {
        try {
          const response = await fetch('http://localhost:8000/api/v1/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: this.registerEmail, password: this.registerPassword }),
          });
          if (response.ok) {
            alert('Registration successful! Please login.');
            this.showLoginForm();
          } else {
            console.error('Registration failed');
          }
        } catch (error) {
          console.error('Registration error:', error);
        }
      },
      showRegisterForm() {
        this.showRegister = true;
      },
      showLoginForm() {
        this.showRegister = false;
      },
    },
  };
  </script>
  
  <style scoped>
  .task-list {
    padding: 20px;
    max-width: 600px;
    margin: 0 auto;
  }
  
  .auth-form {
    margin-bottom: 20px;
  }
  
  .auth-form input {
    margin-bottom: 10px;
    padding: 10px;
    width: 100%;
  }
  
  .task-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #ccc;
  }
  
  .completed {
    text-decoration: line-through;
    color: #888;
  }
  
  input[type="checkbox"] {
    margin-right: 10px;
  }
  </style>
  
