<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Configurações</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <RouterLink to="/admin/dashboard">Início</RouterLink>
            </li>
            <li class="breadcrumb-item active">Configurações</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Configurações Gerais</h3>
            </div>

            <form @submit.prevent="updateSettings()">
              <div class="card-body">
                <div class="form-group">
                  <label for="appName">Nome de exibição</label>
                  <input type="text" class="form-control" id="appName" placeholder="Insira um nome para sua aplicação" v-model="settings.app_name" />
                  <span v-if="errors && errors.app_name" class="text-danger text-sm">{{ errors.app_name[0] }}</span>
                </div>
                <div class="form-group">
                  <label for="dateFormat">Formato de datas</label>
                  <select class="form-control" v-model="settings.date_format">
                    <option value="d/m/Y">DD/MM/AAAA</option>
                    <option value="m/d/Y">MM/DD/AAAA</option>
                    <option value="Y-m-d">AAAA-MM-DD</option>
                    <option value="F j, Y">mês DD, AAAA</option>
                    <option value="j F Y">DD mês AAAA</option>
                  </select>
                  <span v-if="errors && errors.date_format" class="text-danger text-sm">{{ errors.date_format[0] }}</span>
                </div>
                <div class="form-group">
                  <label for="paginationLimit">Número de items exibidos</label>
                  <input type="text" class="form-control" id="paginationLimit" placeholder="Insira um valor limite" v-model="settings.pagination_limit" />
                  <span v-if="errors && errors.pagination_limit" class="text-danger text-sm">{{ errors.pagination_limit[0] }}</span>
                </div>
              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-primary float-right">
                  <i class="fa fa-save mr-1"></i>Salvar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import { useToastr } from "../../toastr";

const settings = ref([]);
const toastr = useToastr();
const errors = ref('');

const getSettings = () => {
  axios.get('/api/settings')
   .then((response) => {
      settings.value = response.data;
   });
};

const updateSettings = () => {
  errors.value = '';

  axios.post('/api/settings', settings.value)
   .then((response) => {
      toastr.success("Configurações atualizadas com sucesso!");
   })
   .catch((error) => {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    }
   });
};

onMounted(() => {
  getSettings();
});
</script>