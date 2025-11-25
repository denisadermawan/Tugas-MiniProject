<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Simple Chat Vue + PHP</title>
  <script src="https://unpkg.com/vue@3"></script>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="app" class="container">
  <h2>💬 Simple Chat Room</h2>

  <!-- Login/Register -->
  <div v-if="!loggedIn">
    <input v-model="username" placeholder="Username"><br><br>
    <input type="password" v-model="password" placeholder="Password"><br><br>
    
    <button v-if="isRegister" @click="register">Register</button>
    <button v-else @click="login">Login</button>
    <br><br>
    <a @click="isRegister = !isRegister">{{ isRegister ? 'Sudah punya akun? Login' : 'Belum punya akun? Daftar' }}</a>
    <p>{{ info }}</p> 
  </div>

  <!-- Chat Room -->
  <div v-else>
    
    <button @click="showProfileEdit = !showProfileEdit" style="margin-bottom: 15px;">
      {{ showProfileEdit ? 'Sembunyikan Menu Edit' : 'Edit Profil' }}
    </button>

    <div class="profile-edit" v-if="showProfileEdit">
      <input v-model="currentProfile" placeholder="Username/Password Lama"><br><br>
      <input v-model="editedProfile" placeholder="Username/Password Baru"><br><br>
    
      <button v-if="editUser" @click="changeUsn">Edit Username</button>
      <button v-else @click="changePass">Edit Password</button>
      <br><br>
      <a @click="editUser = !editUser">{{ editUser ? 'Ingin mengubah password' : 'Ingin mengubah username' }}</a>
      <p>{{ pesan }}</p> 
    </div>

    <div class="user-list">
      <strong>Online Users:</strong>
      <ul>
        <li v-for="u in users" :class="{active: u.status==='online'}">{{ u.username }} ({{ u.status }})</li>
      </ul>
    </div>

    <div class="chat-box">
      <div v-for="msg in message" class="message">
        <strong>{{ msg.username }}:</strong> {{ msg.message }}
      </div>
    </div>

    <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Tulis pesan...">
    <button @click="sendMessage">Kirim</button>
    <br><br>
    
    <button @click="logout">Logout</button>    
  </div>
</div>

<script>
const { createApp } = Vue;

createApp({
  data() {
    return {
      username: '',
      password: '',
      info: '',
      showProfileEdit: false,
      currentProfile: '',
      editedProfile: '',
      pesan: '',
      editUser: false,
      loggedIn: false,
      isRegister: false,
      user: [],
      user_id: [],
      newMessage: '',
      message: [],
      users: []
    }
  },
  methods: {
    register() {
      fetch('backend/register.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username: this.username, password: this.password})
      }).then(r => r.json()).then(d => this.info = d.message);
    },
    login() {
      fetch('backend/login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username: this.username, password: this.password})
      }).then(r => r.json()).then(d => {
        if(d.status === 'success') {
          this.loggedIn = true;
          this.user = d.user;
          this.user_id = d.user.id;
          this.loadMessages();
          this.loadUsers();
          setInterval(this.loadMessages, 2000);
          setInterval(this.loadUsers, 5000);
        } else {
          this.info = d.message;
        }
      });
    },
    loadMessages() {
      fetch('backend/get_message.php')
        .then(r => r.json())
        .then(data => this.message = data);
    },
    loadUsers() {
      fetch('backend/get_users.php')
        .then(r => r.json())
        .then(data => this.users = data);
    },
    sendMessage() {
      if(this.newMessage.trim() === '') return;
      fetch('backend/send_message.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: this.user_id, message: this.newMessage})
      });
      this.newMessage = '';
      this.loadMessages();
    },
    logout() {
      fetch('backend/logout.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: this.user_id})
      }).then(r => r.json()).then(d => {
        if(d.status === 'success') {
          this.loggedIn = false;
          this.isRegister = false;
          this.loadUsers();
        }
      });
    }, 
    changeUsn() {
      if(this.currentProfile.trim() === '' || this.editedProfile.trim() === '') {
        this.pesan = 'Username lama dan baru harus diisi!';
        return;
      }

      fetch('backend/edit_username.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: this.user_id, current_username: this.currentProfile, new_username: this.editedProfile})
      }).then(r => r.json()).then(d => {
        this.pesan = d.message || 'Tidak ada pesan dari server.';
        if(d.status === 'success') {
          this.username = this.editedProfile;
          this.currentProfile = '';
          this.editedProfile = '';
          setTimeout(() => {
            this.showProfileEdit = false;
            this.loadUsers();
          }, 2000);
        }
      });
    },
    changePass() {
      if(this.currentProfile.trim() === '' || this.editedProfile.trim() === '') {
        this.pesan = 'Password lama dan baru harus diisi!';
        return;
      }

      fetch('backend/edit_password.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: this.user_id, current_password: this.currentProfile, new_password: this.editedProfile})
      }).then(r => r.json()).then(d => {
        this.pesan = d.message || 'Tidak ada pesan dari server.';
        if(d.status === 'success') {
          this.username = this.editedProfile;
          this.currentProfile = '';
          this.editedProfile = '';
          setTimeout(() => {
            this.showProfileEdit = false;
            this.loadUsers();
          }, 2000);
        }
      });
    }
  }
}).mount('#app');
</script>
</body>
</html>
