<script setup lang="ts">
import { ref, computed } from 'vue';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Dialog, DialogOverlay, DialogTitle, DialogDescription } from '@headlessui/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

interface User {
  id: number;
  name: string;
  email: string;
  login: string;
  document: string;
  birth: string;
  gender: string;
  marital_status: string;
  education_level: string;
  salary: number;
  courses: Course[];
  experiences: Experience[];
}

interface Course {
  id: number;
  name: string;
  description: string;
  start_date: string;
  end_date: string;
}

interface Experience {
  id: number;
  company: string;
  position: string;
  start_date: string;
  is_current: boolean;
  end_date: string;
  description: string;
}

const props = defineProps<{
    users: { data: User[],
            prev_page_url: string | null, next_page_url: string | null, current_page: number, last_page: number },
    totalSalary: number, averageSalary: number }>();


const formatCurrency = (value: number): string => {
    return value?.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const courseModalOpen = ref<string | null>(null);
const experienceModalOpen = ref<string | null>(null);
</script>

<template>
  <AppLayout>
    <Head title="Lista de currículos - Unimestre sistemas" />
    <Table>
      <TableCaption class="hidden">Lista de usuários e seus currículos.</TableCaption>
      <TableHeader>
        <TableRow>
          <TableHead>Nome</TableHead>
          <TableHead>Email</TableHead>
          <TableHead>Login</TableHead>
          <TableHead>CPF</TableHead>
          <TableHead>Data de Nascimento</TableHead>
          <TableHead>Gênero</TableHead>
          <TableHead>Estado Civil</TableHead>
          <TableHead>Escolaridade</TableHead>
          <TableHead>Pretensão Salarial</TableHead>
          <TableHead>Cursos</TableHead>
          <TableHead>Experiências</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="user in props.users.data" :key="user.id">
          <TableCell>{{ user.name }}</TableCell>
          <TableCell>{{ user.email }}</TableCell>
          <TableCell>{{ user.login }}</TableCell>
          <TableCell>{{ user.document }}</TableCell>
          <TableCell>{{ user.birth }}</TableCell>
          <TableCell>{{ user.gender }}</TableCell>
          <TableCell>{{ user.marital_status }}</TableCell>
          <TableCell>{{ user.education_level }}</TableCell>
          <TableCell :class="{'text-green-500': user.salary < averageSalary, 'text-blue-500': user.salary >= averageSalary}">
            {{ formatCurrency(user.salary) }}
          </TableCell>
          <TableCell>
            <template v-if="user.courses.length">
                <div>
                <button @click="courseModalOpen = user.login" class="text-blue-500 hover:underline"> Cursos </button>
                </div>
            </template>
          </TableCell>
          <TableCell>
            <template v-if="user.experiences.length">
                <div>
                <button @click="experienceModalOpen = user.login" class="text-blue-500 hover:underline"> Experiências </button>
                </div>
            </template>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
    <Dialog :open="courseModalOpen !== null || experienceModalOpen !== null" @close="courseModalOpen = experienceModalOpen = null">
      <DialogOverlay class="fixed inset-0 bg-black opacity-30" />
      <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
          <DialogTitle class="text-lg font-bold">
            <template v-if="courseModalOpen !== null">Cursos</template>
            <template v-else>Experiências</template>
          </DialogTitle>
          <DialogDescription>
            <hr class="my-5">
            <ul v-if="courseModalOpen !== null">
              <li v-for="course in props.users.data.find(user => user.login === courseModalOpen)?.courses" :key="course.id">
                <strong>{{ course.name }}</strong>: {{ course.description }} ({{ course.start_date }} - {{ course.end_date }})
              </li>
            </ul>
            <ul v-else>
              <li v-for="experience in props.users.data.find(user => user.login === experienceModalOpen)?.experiences" :key="experience.id">
                <strong>{{ experience.company }}</strong>: {{ experience.position }} ({{ experience.start_date }} - {{ experience.is_current ? 'Presente' : experience.end_date }})
                <p>Descrição: {{ experience.description }}</p>
              </li>
            </ul>
          </DialogDescription>
          <hr class="my-5">
          <div class="flex justify-end">
            <button @click="courseModalOpen = experienceModalOpen = null" class="px-4 py-2 bg-gray-100 rounded">Fechar</button>
          </div>
        </div>
      </div>
    </Dialog>
    <div class="mt-4">
      <p>Total de Pretensão Salarial: {{ formatCurrency(totalSalary) }}</p>
      <p>Média de Pretensão Salarial: {{ formatCurrency(averageSalary) }}</p>
    </div>
    <div class="flex justify-between items-center mt-4">
        <a
            :disabled="props.users.current_page == 1 || props.users.last_page < 2"
            :href="props.users.current_page == 1 ? '#' : `${route('curriculos.lista')}?page=${props.users.current_page - 1 || 1}`"
            class="px-4 py-2 bg-gray-200 rounded"
            :class="{ 'cursor-not-allowed disabled opacity-50': props.users.current_page == 1 || props.users.last_page < 2 }"
        >
            Anterior
        </a>
        <span>Página {{ props.users.current_page }} de {{ props.users.last_page }}</span>
        <a
            :disabled=" props.users.current_page == props.users.last_page || props.users.last_page < 2"
            :href="props.users.current_page == props.users.last_page || props.users.last_page < 2 ? '#' : `${route('curriculos.lista')}?page=${props.users.current_page + 1}`"
            class="px-4 py-2 bg-gray-200 rounded"
            :class="{ 'cursor-not-allowed disabled opacity-50': props.users.current_page == props.users.last_page || props.users.last_page < 2 }"
        >
            Próxima
        </a>
    </div>

  </AppLayout>
</template>
<style scoped>
/* Garantir que a tabela tenha um layout flexível */
.table {
  table-layout: auto;
}
</style>
