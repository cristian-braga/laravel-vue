<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Compromissos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <RouterLink to="/admin/dashboard">Início</RouterLink>
            </li>
            <li class="breadcrumb-item active">Compromissos</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="d-flex justify-content-between mb-2">
            <div>
              <RouterLink to="/admin/appointments/create">
                <button class="btn btn-primary">
                  <i class="fa fa-plus-circle mr-1"></i> 
                  Cadastrar
                </button>
              </RouterLink>
            </div>
            <div class="btn-group">
              <button type="button" :class="`btn ${typeof selectedStatus === 'undefined' ? 'btn-secondary' : 'btn-default'}`"  @click="getAppointments()">
                <span class="mr-1">TODOS</span>
                <span class="badge badge-pill badge-info">{{ appointmentsCount }}</span>
              </button>

              <button v-for="status in appointmentStatus" :key="status.value" type="button" :class="`btn ${selectedStatus === status.value ? 'btn-secondary' : 'btn-default'}`" @click="getAppointments(status.value)">
                <span class="mr-1">{{ status.name }}</span>
                <span :class="`badge badge-pill badge-${status.color}`">{{ status.count }}</span>
              </button>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Início</th>
                    <th scope="col">Fim</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ appointment.client.first_name }} {{ appointment.client.last_name }}</td>
                    <td>{{ appointment.start_time }}</td>
                    <td>{{ appointment.end_time }}</td>
                    <td>
                      <span :class="`badge badge-${appointment.status.color}`">{{ appointment.status.name }}</span>
                    </td>
                    <td>
                      <RouterLink :to="`/admin/appointments/${appointment.id}/edit`">
                        <i class="fa fa-edit mr-2"></i>
                      </RouterLink>

                      <a href="#" @click.prevent="deleteAppointment(appointment.id)">
                        <i class="fa fa-trash text-danger"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";
import Swal from "sweetalert2";

const appointments = ref([]);
const appointmentStatus = ref([]);
const selectedStatus = ref();

const getAppointments = (status) => {
  selectedStatus.value = status;

  const params = {};

  if (status) {
    params.status = status;
  }

  axios.get('/api/appointments', {
    params
  })
    .then((response) => {
      appointments.value = response.data;
    });
};

const getAppointmentsStatus = () => {
  axios.get('/api/appointments-status')
    .then((response) => {
      appointmentStatus.value = response.data;
    });
};

const updateAppointmentStatusCount = (id) => {
  const deletedAppointmentStatus = appointments.value.data.find(appointment => appointment.id === id).status.name;

  const statusToUpdate = appointmentStatus.value.find(status => status.name === deletedAppointmentStatus);

  statusToUpdate.count--;
};

const deleteAppointment = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!"
  }).then((result) => {
    if (result.isConfirmed) {
      axios.delete(`/api/appointments/${id}`)
        .then((response) => {
          updateAppointmentStatusCount(id);

          appointments.value.data = appointments.value.data.filter(appointment => appointment.id !== id);

          Swal.fire({
            title: "Deleted!",
            text: "Your file has been deleted.",
            icon: "success"
          });
        });
    }
  });
};

const appointmentsCount = computed(() => {
  return appointmentStatus.value.map(status => status.count).reduce((acc, value) => acc + value, 0);
});

onMounted(() => {
  getAppointments();
  getAppointmentsStatus();
});
</script>