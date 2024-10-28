<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Usuários</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <RouterLink to="/admin/dashboard">Início</RouterLink>
            </li>
            <li class="breadcrumb-item active">Usuários</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="d-flex justify-content-between mb-2">
        <div class="d-flex">
          <button type="button" class="btn btn-primary mb-2" @click="addUser">
            <i class="fa fa-plus-circle mr-1"></i> 
            Cadastrar
          </button>
          <div v-if="selectedUsers.length > 0">
            <button type="button" class="btn btn-danger mb-2 ml-2" @click="bulkDelete">
              <i class="fa fa-trash mr-1"></i> 
              Excluir
            </button>
            <span class="ml-2"><em>{{ selectedUsers.length }} selecionado(s)</em></span>
          </div>
        </div>
        <div>
          <input type="text" class="form-control" placeholder="Buscar..." v-model="searchQuery">
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" v-model="selectAll" @change="selectAllUsers" />
                </th>
                <th style="width: 10px">#</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Data de Cadastro</th>
                <th>Permissão</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody v-if="users.data.length > 0">
              <UserItem v-for="(user, index) in users.data"
                :key="user.id"
                :user=user
                :index=index
                :select-all="selectAll"
                @edit-user="editUser"
                @confirm-user-deletion="confirmUserDeletion"
                @toggle-selection="toggleSelection"
              />
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="7" class="text-center">Nenhum resultado encontrado...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="d-flex justify-content-center">
        <Bootstrap5Pagination :data="users" @pagination-change-page="getUsers" />
      </div>
    </div>
  </div>

  <div class="modal fade" id="userFormModal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="userFormModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userFormModalLabel">
            <span v-if="!editing">Cadastrar usuário</span>
            <span v-else>Editar usuário</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors }" :initial-values="formValues">
          <div class="modal-body">
            <div class="form-group">
              <label for="name">Nome</label>
              <Field type="text" class="form-control" :class="{ 'is-invalid': errors.name }" id="name" name="name"
                aria-describedby="nameHelp" placeholder="Nome completo" />
              <span class="invalid-feedback">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <Field type="email" class="form-control" :class="{ 'is-invalid': errors.email }" id="email" name="email"
                aria-describedby="nameHelp" placeholder="Email" />
              <span class="invalid-feedback">{{ errors.email }}</span>
            </div>

            <div class="form-group">
              <label for="email">Senha</label>
              <Field type="password" class="form-control" :class="{ 'is-invalid': errors.password }" id="password"
                name="password" aria-describedby="nameHelp" placeholder="Senha" />
              <span class="invalid-feedback">{{ errors.password }}</span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
          </div>
        </Form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">
            <span>Excluir usuário</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5>Realmente deseja excluir este usuário?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
          <button @click.prevent="deleteUser" type="button" class="btn btn-primary btn-sm">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { useToastr } from "../../toastr.js";
import { debounce } from "lodash";
import UserItem from "./UserItem.vue";
import { Bootstrap5Pagination } from 'laravel-vue-pagination';

const users = ref({ data: [] });
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const toastr = useToastr();
const searchQuery = ref(null);
const selectedUsers = ref([]);
const selectAll = ref(false);
const userId = ref(null);

const getUsers = (page = 1) => {
  axios.get(`/api/users?page=${page}`, {
    params: {
      query: searchQuery.value
    }
  })
    .then((response) => {
      users.value = response.data;

      selectedUsers.value = [];

      selectAll.value = false;
    });
};

const addUser = () => {
  editing.value = false;

  $('#userFormModal').modal('show');
};

const handleSubmit = (values, actions) => {
  if (editing.value) {
    updateUser(values, actions);
  } else {
    createUser(values, actions);
  }
};

const createUser = (values, { resetForm, setErrors }) => {
  axios.post("/api/users", values).then((response) => {
    users.value.data.unshift(response.data);

    $("#userFormModal").modal("hide");

    resetForm();

    toastr.success("Usuário cadastrado com sucesso!");
  }).catch((error) => {
    if (error.response.data.errors) {
      setErrors(error.response.data.errors);
    }
  });
};

const editUser = (user) => {
  editing.value = true;

  form.value.resetForm();

  formValues.value = {
    id: user.id,
    name: user.name,
    email: user.email
  };

  $('#userFormModal').modal('show');
};

const updateUser = (values, { resetForm, setErrors }) => {
  axios.put(`/api/users/${formValues.value.id}`, values)
    .then((response) => {
      const index = users.value.findIndex((user) => user.id === response.data.id);

      users.value[index] = response.data;

      $("#userFormModal").modal("hide");

      resetForm();

      getUsers();

      toastr.success("Usuário atualizado com sucesso!");
    }).catch((error) => {
      if (error.response.data.errors) {
        setErrors(error.response.data.errors);
      }
    });
};

const toggleSelection = (user) => {
  const index = selectedUsers.value.indexOf(user.id);

  if (index === -1) {
    selectedUsers.value.push(user.id);
  } else {
    selectedUsers.value.splice(index, 1);
  }
};

const selectAllUsers = () => {
  if (selectAll.value) {
    selectedUsers.value = users.value.data.map(user => user.id);
  } else {
    selectedUsers.value = [];
  }
};

const confirmUserDeletion = (id) => {
  userId.value = id;

  $("#deleteUserModal").modal("show");
};

const deleteUser = () => {
  axios.delete(`/api/users/${userId.value}`).then(() => {
    $("#deleteUserModal").modal("hide");

    toastr.success("Usuário excluído com sucesso!");

    users.value.data = users.value.data.filter(user => user.id !== userId.value);
  });
};

const bulkDelete = () => {
  axios.delete('/api/users', {
    data: {
      ids: selectedUsers.value
    }
  })
    .then((response) => {
      users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));

      selectedUsers.value = [];

      selectAll.value = false;

      toastr.success(response.data.message);
    });
};

const createUserSchema = yup.object({
  name: yup.string().required("Campo obrigatório!"),
  email: yup.string().email("Insira um email válido!").required("Campo obrigatório!"),
  password: yup.string().required("Campo obrigatório!").min(8, "A senha precisa ter no mínimo 8 caracteres!")
});

const editUserSchema = yup.object({
  name: yup.string().required("Campo obrigatório!"),
  email: yup.string().email("Insira um email válido!").required("Campo obrigatório!"),
  password: yup.string().when((password, schema) => {
    return password ? schema.required("Campo obrigatório!").min(8, "A senha precisa ter no mínimo 8 caracteres!") : schema;
  })
});

watch(formValues, (newValues) => {
  if (form.value) {
    form.value.setValues(newValues);
  }
});

watch(searchQuery, debounce(() => {
  getUsers();
}, 300));

onMounted(() => {
  getUsers();
});
</script>