<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Perfil</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <RouterLink to="/admin/dashboard">Início</RouterLink>
            </li>
            <li class="breadcrumb-item active">Perfil</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">
                <input type="file" class="d-none" ref="fileInput" @change="handleFileChange" />
                <img class="profile-user-img img-circle" :src="profilePictureUrl ? profilePictureUrl : form.avatar" alt="User profile picture" @click="openFileInput" />
              </div>

              <h3 class="profile-username text-center">{{ form.name }}</h3>

              <p class="text-muted text-center">{{ form.role }}</p>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="card">
            <div class="card-header p-2">
              <ul class="nav nav-pills">
                <li class="nav-item">
                  <a class="nav-link active" href="#profile" data-toggle="tab">
                    <i class="fa fa-user mr-1"></i> Editar
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#changePassword" data-toggle="tab">
                    <i class="fa fa-key mr-1"></i> Alterar Senha
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content">
                <div class="tab-pane active" id="profile">
                  <form @submit.prevent="updateProfile" class="form-horizontal">
                    <div class="form-group row">
                      <label for="inputName" class="col-sm-2 col-form-label">Nome</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" placeholder="Nome" v-model="form.name" />
                        <span v-if="errors && errors.name" class="text-danger text-sm">{{ errors.name[0] }}</span>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail" placeholder="Email" v-model="form.email" />
                        <span v-if="errors && errors.email" class="text-danger text-sm">{{ errors.email[0] }}</span>
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-success">
                          <i class="fa fa-save mr-1"></i> Salvar
                        </button>
                      </div>
                    </div>
                  </form>
                </div>

                <div class="tab-pane" id="changePassword">
                  <form class="form-horizontal">
                    <div class="form-group row">
                      <label for="currentPassword" class="col-sm-3 col-form-label">Senha atual</label >
                      <div class="col-sm-9">
                        <input type="password" class="form-control" id="currentPassword" placeholder="Senha atual" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="newPassword" class="col-sm-3 col-form-label">Nova senha</label>
                      <div class="col-sm-9">
                        <input type="password" class="form-control" id="newPassword" placeholder="Nova senha" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="passwordConfirmation" class="col-sm-3 col-form-label" >Confirmar senha</label>
                      <div class="col-sm-9">
                        <input type="password" class="form-control" id="passwordConfirmation" placeholder="Confirmar senha" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <div class="offset-sm-3 col-sm-9">
                        <button type="submit" class="btn btn-success">
                          <i class="fa fa-save mr-1"></i> Salvar
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import { useToastr } from "../../toastr.js";

const toastr = useToastr();
const errors = ref('');
const fileInput = ref(null);
const profilePictureUrl = ref(null);

const form = ref({
  name: '',
  email: '',
  role: ''
});

const getUser = () => {
  axios.get('/api/profile')
    .then((response) => {
      form.value = response.data;
    });
};

const updateProfile = () => {
  axios.put('/api/profile', form.value)
    .then((response) => {
      toastr.success("Perfil alterado com sucesso!");
    })
    .catch((error) => {
      if (error.response && error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
    });
};

const openFileInput = () => {
  fileInput.value.click();
};

const handleFileChange = (event) => {
  const file = event.target.files[0];

  profilePictureUrl.value = URL.createObjectURL(file);

  const formData = new FormData();

  formData.append('profile_picture', file);

  axios.post('/api/upload-profile-image', formData)
    .then((response) => {
      toastr.success("Imagem alterada com sucesso!");
    });
};

onMounted(() => {
  getUser();
});
</script>

<style scoped>
.profile-user-img:hover {
  background-color: #0000ff;
  cursor: pointer;
  transition: .3s;
}
</style>
