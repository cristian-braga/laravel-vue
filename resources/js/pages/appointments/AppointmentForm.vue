<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">
            <span v-if="!editMode">Cadastrar </span>
            <span v-else>Editar </span>
            Compromisso
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <RouterLink to="/admin/dashboard">Início</RouterLink>
            </li>
            <li class="breadcrumb-item">
              <RouterLink to="/admin/appointments">Compromissos</RouterLink>
            </li>
            <li class="breadcrumb-item active">
              <span v-if="!editMode">Cadastrar </span>
              <span v-else>Editar </span>
            </li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <Form @submit="handleSubmit" v-slot:default="{ errors }">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="title">Título</label>
                      <input type="text" class="form-control" :class="{ 'is-invalid': errors.title }" id="title" placeholder="Insira o título" v-model="form.title" />
                      <span class="invalid-feedback">{{ errors.title }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="client">Nome do cliente</label>
                      <select id="client" class="form-control" :class="{ 'is-invalid': errors.client_id }" v-model="form.client_id">
                        <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.first_name }} {{ client.last_name }}</option>
                      </select>
                      <span class="invalid-feedback">{{ errors.client_id }}</span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="date">Início</label>
                      <input type="text" class="form-control flatpickr" :class="{ 'is-invalid': errors.start_time }" id="startTime" v-model="form.start_time" />
                      <span class="invalid-feedback">{{ errors.start_time }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="time">Fim</label>
                      <input type="text" class="form-control flatpickr" :class="{ 'is-invalid': errors.end_time }" id="endTime" v-model="form.end_time" />
                      <span class="invalid-feedback">{{ errors.end_time }}</span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="description">Descrição</label>
                  <textarea class="form-control" :class="{ 'is-invalid': errors.description }" id="description" rows="8" placeholder="Insira a descrição..." v-model="form.description"></textarea>
                  <span class="invalid-feedback">{{ errors.description }}</span>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
              </Form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useToastr } from "../../toastr.js";
import { Form } from "vee-validate";
import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
import "flatpickr/dist/themes/light.css";

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const clients = ref();
const editMode = ref(false);

const form = reactive({
  title: '',
  client_id: '',
  start_time: '',
  end_time: '',
  description: '',
});

const handleSubmit = (values, { setErrors }) => {
  if (editMode.value) {
    editAppointment(values, setErrors);
  } else {
    createAppointment(values, setErrors);
  }
};

const createAppointment = () => {
  axios.post('/api/appointments/store', form)
    .then((response) => {
      router.push('/admin/appointments');

      toastr.success("Compromisso agendado com sucesso!");
    }).catch((error) => {
    if (error.response.data.errors) {
      setErrors(error.response.data.errors);
    }
  });
};

const editAppointment = () => {
  axios.put(`/api/appointments/${route.params.id}`, form)
    .then((response) => {
      router.push('/admin/appointments');

      toastr.success("Compromisso atualizado com sucesso!");
    }).catch((error) => {
    if (error.response.data.errors) {
      setErrors(error.response.data.errors);
    }
  });
};

const getClients = () => {
  axios.get('/api/clients')
    .then((response) => {
      clients.value = response.data;
    });
};

const getAppointment = () => {
  axios.get(`/api/appointments/${route.params.id}/edit`)
    .then(({ data }) => {
      form.title = data.title;
      form.client_id = data.client_id;
      form.start_time = data.formatted_start_time;
      form.end_time = data.formatted_end_time;
      form.description = data.description;
    });
};

onMounted(() => {
  if (route.name === 'admin.appointments.edit') {
    editMode.value = true;

    getAppointment();
  }

  flatpickr(".flatpickr", {
    enableTime: true,
    dateFormat: "d/m/Y H:i",
    time_24hr: true,
    locale: Portuguese
  });

  getClients();
});
</script>