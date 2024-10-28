<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Painel</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Painel</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <div class="d-flex justify-content-between">
                <h3>{{ totalAppointmentsCount }}</h3>
                <select style="height: 2rem; outline: 2px solid transparent" class="px-1 rounded border-0" v-model="selectedAppointmentStatus" @change="getAppointmentsCount()">
                  <option value="all">Todos</option>
                  <option value="agendado">Agendados</option>
                  <option value="confirmado">Confirmados</option>
                  <option value="cancelado">Cancelados</option>
                </select>
              </div>
              <p>Compromissos</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <RouterLink to="/admin/appointments" class="small-box-footer">
              Compromissos
              <i class="fas fa-arrow-circle-right ml-1"></i>
            </RouterLink>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <div class="d-flex justify-content-between">
                <h3>{{ totalUsersCount }}</h3>
                <select style="height: 2rem; outline: 2px solid transparent" class="px-1 rounded border-0" v-model="selectedDateRange" @change="getUsersCount()">
                  <option value="today">Hoje</option>
                  <option value="30_days">30 dias</option>
                  <option value="60_days">60 dias</option>
                  <option value="year">Ano</option>
                  <option value="month_to_date">Do mês até hoje</option>
                  <option value="year_to_date">Do ano até hoje</option>
                </select>
              </div>
              <p>Usuários</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <RouterLink to="/admin/users" class="small-box-footer">
              Usuários
              <i class="fas fa-arrow-circle-right ml-1"></i>
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const selectedAppointmentStatus = ref('all');
const totalAppointmentsCount = ref(0);
const selectedDateRange = ref('today');
const totalUsersCount = ref(0);

const getAppointmentsCount = () => {
  axios.get('/api/stats/appointments', {
    params: {
      status: selectedAppointmentStatus.value
    }
  })
    .then((response) => {
      totalAppointmentsCount.value = response.data.totalAppointmentsCount;
    });
};

const getUsersCount = () => {
  axios.get('/api/stats/users', {
    params: {
      date_range: selectedDateRange.value
    }
  })
    .then((response) => {
      totalUsersCount.value = response.data.totalUsersCount;
    });
};

onMounted(() => {
  getAppointmentsCount();
  getUsersCount();
});
</script>