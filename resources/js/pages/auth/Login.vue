<template>
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="#" class="h1"><b>Admin</b>Login</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Faça login para iniciar sua sessão</p>
        <div v-if="errorMessage" class="alert alert-danger" role="alert">
          {{ errorMessage }}
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" v-model="form.email" />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Senha" v-model="form.password" />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember" />
                <label for="remember"> Lembrar </label>
              </div>
            </div>

            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <span v-else>Sign In</span>
              </button>
            </div>
          </div>
        </form>

        <p class="mb-1">
          <a href="forgot-password.html">Esqueci a senha</a>
        </p>
      </div>
    </div>
  </div>
  <div>
    test@example.com <br>
    password
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const errorMessage = ref("");
const loading = ref(false);

const form = reactive({
  email: '',
  password: ''
});

const handleSubmit = () => {
  loading.value = true;

  errorMessage.value = "";

  axios.post('/login', form)
    .then(() => {
      router.push('/admin/dashboard');
    })
    .catch((error) => {
      errorMessage.value = error.response.data.message;
    })
    .finally(() => {
      loading.value = false;
    });
};
</script>