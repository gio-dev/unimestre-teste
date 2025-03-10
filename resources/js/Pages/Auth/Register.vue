<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { useIMask } from 'vue-imask';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
  duration: 1000,
  position: {
    x: 'right',
    y: 'top',
  },
});

interface Course {
    name: string;
    description: string;
    start_date: string;
    end_date?: string;
}

interface Experience {
    company: string;
    position: string;
    start_date: string;
    end_date?: string;
    is_current: boolean;
    description: string;
}

interface FormErrors {
    name?: string;
    email?: string;
    login?: string;
    password?: string;
    password_confirmation?: string;
    cpf?: string;
    birth?: string;
    gender?: string;
    marital_status?: string;
    education_level?: string;
    salary?: string;
    courses?: { [key: string]: string }[];
    experiences?: { [key: string]: string }[];
    [key: string]: any; // Add index signature
}

const form = useForm({
    errors: {} as FormErrors,
    name: '',
    email: '',
    login: '',
    password: '',
    password_confirmation: '',
    cpf: '',
    birth: '',
    gender: '',
    marital_status: '',
    education_level: '',
    salary: '',
    courses: [] as Record<string, any>[], // Definição explícita do tipo
    experiences: [] as Record<string, any>[],
});

const genders = ref<Array<string>>(['Masculino', 'Feminino', 'Prefiro não dizer']);
const maritalStatuses = ref<Array<string>>(['Solteiro', 'Casado', 'Divorciado', 'Viúvo']);
const educationLevels = ref<Array<string>>(['Ensino Fundamental', 'Ensino Médio', 'Ensino Superior', 'Pós-graduação', 'Mestrado', 'Doutorado']);

const addCourse = () => {
    form.courses.push({ name: '', description: '', start_date: '', end_date: '' });
};

const addExperience = () => {
    form.experiences.push({ company: '', position: '', start_date: '', end_date: '', is_current: false, description: '' });
};

const courses = computed(() => form.courses);
const experiences = computed(() => form.experiences);

const isFormValid = computed(() => {
    const isCoursesValid = form.courses.every(course => validateEndDate(course.start_date, course.end_date));
    const isExperiencesValid = form.experiences.every(experience => validateEndDate(experience.start_date, experience.end_date));
    const isPasswordValid = isEditMode.value || (form.password && form.password_confirmation && form.password === form.password_confirmation);
    return form.name && form.email && form.login && form.cpf && form.birth && form.gender && form.marital_status && isCoursesValid && isExperiencesValid && isPasswordValid;
});

const validateEmail = (email: string) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

const validateCPF = (cpf: string) => {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
    let sum = 0;
    let remainder;
    for (let i = 1; i <= 9; i++) sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.substring(9, 10))) return false;
    sum = 0;
    for (let i = 1; i <= 10; i++) sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    return remainder === parseInt(cpf.substring(10, 11));
};

const validateBirthDate = (birth: string) => {
    const birthDate = new Date(birth);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    const dayDiff = today.getDate() - birthDate.getDate();
    return age > 16 || (age === 16 && (monthDiff > 0 || (monthDiff === 0 && dayDiff >= 0)));
};

const validateEndDate = (startDate: string, endDate: string) => {
    if (!endDate) return true;
    return new Date(endDate) >= new Date(startDate);
};

const validatePassword = (password: string) => {
    return password.length >= 6;
};

const showPassword = ref(false); // Toggle para mostrar a senha
const showPasswordConfirmation = ref(false); // Toggle para mostrar a confirmação da senha

const { el: cpfRef } = useIMask({
  mask: '000.000.000-00',
  lazy: true,
  placeholderChar: '_',
});

watch(() => form.email, (newVal) => {
    if (typeof newVal === 'string' && !validateEmail(newVal)) {
        form.errors.email = 'Email inválido';
    } else {
        form.errors.email = '';
    }
});

watch(() => form.cpf, (newVal) => {
    if (typeof newVal === 'string') {
        if (!validateCPF(newVal)) {
            form.errors.cpf = 'CPF inválido';
        } else {
            form.errors.cpf = '';
        }
    }
});

watch(() => form.birth, (newVal) => {
    if (typeof newVal === 'string' && !validateBirthDate(newVal)) {
        form.errors.birth = 'Data de nascimento inválida. O usuário deve ter pelo menos 16 anos.';
    } else {
        form.errors.birth = '';
    }
});

watch(() => form.password, (newVal) => {
    if (typeof newVal === 'string' && !validatePassword(newVal)) {
        form.errors.password = 'A senha deve ter pelo menos 6 dígitos';
    } else {
        form.errors.password = '';
    }
});

watch(() => form.password_confirmation, (newVal) => {
    if (!isEditMode.value) {
        if (typeof newVal === 'string' && newVal !== form.password) {
            form.errors.password_confirmation = 'A confirmação da senha não corresponde à senha';
        } else {
            form.errors.password_confirmation = '';
        }
    }
});

watch(() => form.courses, (newVal) => {
    newVal.forEach((course, index) => {
        if (!validateEndDate(course.start_date, course.end_date)) {
            form.errors[`courses.${index}.end_date`] = 'A data de término não pode ser menor que a data de início';
        } else {
            form.errors[`courses.${index}.end_date`] = '';
        }
    });
}, { deep: true });

watch(() => form.experiences, (newVal) => {
    newVal.forEach((experience, index) => {
        if (!validateEndDate(experience.start_date, experience.end_date)) {
            form.errors[`experiences.${index}.end_date`] = 'A data de término não pode ser menor que a data de início';
        } else {
            form.errors[`experiences.${index}.end_date`] = '';
        }
    });
}, { deep: true });

const removeCourse = (index: number) => {
    form.courses.splice(index, 1);
};

const removeExperience = (index: number) => {
    form.experiences.splice(index, 1);
};

const submit = () => {
    if (isFormValid.value) {
        const routeName = isEditMode.value ? 'alterar-curriculo' : 'register.store';
        form.post(route(routeName), {
            preserveState: true,
            onStart: () => {
                form.processing = true;
            },
            onFinish: () => {
                form.processing = false;
                if (!form.hasErrors) {
                    form.reset('password', 'password_confirmation', 'courses', 'experiences');
                    notyf.success(isEditMode.value ? 'Currículo atualizado com sucesso!' : 'Currículo cadastrado com sucesso!');
                } else {
                    notyf.error(isEditMode.value ? 'Erro ao atualizar currículo. Verifique os campos e tente novamente.' : 'Erro ao cadastrar currículo. Verifique os campos e tente novamente.');
                }
            },
        });
    }
};

// Carregar dados do usuário logado
interface User {
    id: number;
    name: string;
    email: string;
    login: string;
    cpf: string;
    birth: string;
    gender: string;
    marital_status: string;
    education_level: string;
    salary: string;
}

interface PageProps {
    auth: {
        user: User;
    };
    courses: Course[];
    experiences: Experience[];
    [key: string]: any; // Add index signature
}

const page = usePage<PageProps>();

const isEditMode = ref(false);

onMounted(() => {
    if (page.props.user) {
        isEditMode.value = true;
        form.name = page.props.user.name;
        form.email = page.props.user.email;
        form.login = page.props.user.login;
        form.cpf = page.props.user.document;
        form.birth = page.props.user.birth;
        form.gender = page.props.user.gender;
        form.marital_status = page.props.user.marital_status;
        form.education_level = page.props.user.education_level;
        form.salary = page.props.user.salary;
        form.courses = page.props.courses;
        form.experiences = page.props.experiences;
    }
    if (page.props.errors.page) {
        notyf.error(page.props.errors.page);
    }
});
</script>

<template>
    <GuestLayout>
        <Head :title="isEditMode ? 'Edição - Unimestre sistemas' : 'Cadastro - Unimestre sistemas'" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nome" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="login" value="Login" />
                <TextInput id="login" type="text" class="mt-1 block w-full" v-model="form.login" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Senha" />
                <div class="relative">
                    <TextInput id="password" :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full" v-model="form.password" autocomplete="new-password" />
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.3 21.3 0 0 1 5.07-6.14"></path>
                            <path d="M1 1l22 22"></path>
                        </svg>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4" v-if="!isEditMode">
                <InputLabel for="password_confirmation" value="Confirmar Senha" />
                <div class="relative">
                    <TextInput id="password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'" class="mt-1 block w-full" v-model="form.password_confirmation" autocomplete="new-password" />
                    <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <svg v-if="!showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.3 21.3 0 0 1 5.07-6.14"></path>
                            <path d="M1 1l22 22"></path>
                        </svg>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <InputLabel for="cpf" value="CPF" />
                <input
                    id="cpf"
                    type="text"
                    v-model="form.cpf"
                    ref="cpfRef"
                    placeholder="___.___.___-__"
                    required
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mt-1 block w-full"
                />
                <InputError class="mt-2" :message="form.errors.cpf" />
            </div>

            <div class="mt-4">
                <InputLabel for="birth" value="Data de Nascimento" />
                <TextInput id="birth" type="date" class="mt-1 block w-full" v-model="form.birth" required />
                <InputError class="mt-2" :message="form.errors.birth" />
            </div>

            <div class="mt-4">
                <InputLabel for="gender" value="Sexo" />
                <Select v-model="form.gender">
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione o sexo" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Sexo</SelectLabel>
                            <SelectItem v-for="gender in genders" :key="gender" :value="gender">
                                {{ gender }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <InputError class="mt-2" :message="form.errors.gender" />
            </div>

            <div class="mt-4">
                <InputLabel for="marital_status" value="Estado Civil" />
                <Select v-model="form.marital_status">
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione o estado civil" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Estado Civil</SelectLabel>
                            <SelectItem v-for="status in maritalStatuses" :key="status" :value="status">
                                {{ status }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <InputError class="mt-2" :message="form.errors.marital_status" />
            </div>

            <div class="mt-4">
                <InputLabel for="education_level" value="Escolaridade" />
                <Select v-model="form.education_level">
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione a escolaridade" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Escolaridade</SelectLabel>
                            <SelectItem v-for="level in educationLevels" :key="level" :value="level">
                                {{ level }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <InputError class="mt-2" :message="form.errors.education_level" />
            </div>

            <div class="mt-4">
                <InputLabel for="salary" value="Pretensão Salarial" />
                <TextInput id="salary" type="number" class="mt-1 block w-full" v-model.number="form.salary" />
                <InputError class="mt-2" :message="form.errors.salary" />
            </div>

            <div class="mt-4">
                <h3 class="text-lg font-medium">Cursos/Especializações</h3>
                <div v-for="(course, index) in courses" :key="index" class="mt-4">
                    <hr class="my-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold">{{ index + 1 }}: {{ course.name || 'Novo Curso' }}</h4>
                        <button type="button" @click="removeCourse(index)" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9l-3-3a1 1 0 00-1.414 1.414L8.586 10l-3 3a1 1 0 001.414 1.414l3-3 3-3a1 1 0 001.414-1.414l-3-3z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <InputLabel :for="'course_name_' + index" value="Nome do Curso" />
                    <TextInput :id="'course_name_' + index" type="text" class="mt-1 block w-full" v-model="course.name" required />
                    <InputError class="mt-2" :message="form.errors['courses.' + index + '.name']" />

                    <InputLabel :for="'course_description_' + index" value="Descrição" class="mt-4" />
                    <TextInput :id="'course_description_' + index" type="text" class="mt-1 block w-full" v-model="course.description" required />
                    <InputError class="mt-2" :message="form.errors['courses.' + index + '.description']" />

                    <InputLabel :for="'course_start_date_' + index" value="Data de Início" class="mt-4" />
                    <TextInput :id="'course_start_date_' + index" type="date" class="mt-1 block w-full" v-model="course.start_date" required />
                    <InputError class="mt-2" :message="form.errors['courses.' + index + '.start_date']" />

                    <InputLabel :for="'course_end_date_' + index" value="Data de Término" class="mt-4" />
                    <TextInput :id="'course_end_date_' + index" type="date" class="mt-1 block w-full" v-model="course.end_date" />
                    <InputError class="mt-2" :message="form.errors['courses.' + index + '.end_date']" />
                </div>
                <PrimaryButton class="mt-4" @click.prevent="addCourse">Adicionar Curso</PrimaryButton>
            </div>

            <div class="mt-4">
                <hr class="my-6">
                <h3 class="text-lg font-medium">Experiências Profissionais</h3>
                <div v-for="(experience, index) in experiences" :key="index" class="mt-4">
                    <hr class="my-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-semibold">{{ index + 1 }}: {{ experience.company || 'Nova Experiência' }}</h4>
                        <button type="button" @click="removeExperience(index)" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9l-3-3a1 1 0 00-1.414 1.414L8.586 10l-3 3a1 1 0 001.414 1.414l3-3 3-3a1 1 0 001.414-1.414l-3-3z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <InputLabel :for="'experience_company_' + index" value="Empresa" />
                    <TextInput :id="'experience_company_' + index" type="text" class="mt-1 block w-full" v-model="experience.company" required />
                    <InputError class="mt-2" :message="form.errors['experiences.' + index + '.company']" />

                    <InputLabel :for="'experience_position_' + index" value="Cargo" class="mt-4" />
                    <TextInput :id="'experience_position_' + index" type="text" class="mt-1 block w-full" v-model="experience.position" required />
                    <InputError class="mt-2" :message="form.errors['experiences.' + index + '.position']" />

                    <InputLabel :for="'experience_start_date_' + index" value="Data de Início" class="mt-4" />
                    <TextInput :id="'experience_start_date_' + index" type="date" class="mt-1 block w-full" v-model="experience.start_date" required />
                    <InputError class="mt-2" :message="form.errors['experiences.' + index + '.start_date']" />

                    <InputLabel :for="'experience_end_date_' + index" value="Data de Término" class="mt-4" />
                    <TextInput :id="'experience_end_date_' + index" type="date" class="mt-1 block w-full" :class="{ 'opacity-50 background-gray-100': experience.is_current }" v-model="experience.end_date" :disabled="experience.is_current" />
                    <InputError class="mt-2" :message="form.errors['experiences.' + index + '.end_date']" />

                    <InputLabel :for="'experience_is_current_' + index" value="Emprego Atual" class="mt-4" />
                    <input :id="'experience_is_current_' + index" type="checkbox" class="rounded" v-model="experience.is_current" :checked="experience.is_current" />
                    <InputError class="mt-2" :message="form.errors['experiences.' + index + '.is_current']" />

                    <InputLabel :for="'experience_description_' + index" value="Descrição" class="mt-4" />
                    <TextInput :id="'experience_description_' + index" type="text" class="mt-1 block w-full" v-model="experience.description" required />
                    <InputError class="mt-2" :message="form.errors['experiences.' + index + '.description']" />
                </div>
                <PrimaryButton class="mt-4" @click.prevent="addExperience">Adicionar Experiência</PrimaryButton>
            </div>

            <div class="mt-8 flex border-t border-gray-100 pt-8 items-center justify-between">
                <Link v-if="!isEditMode" :href="route('login')" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Já registrado?
                </Link>

                <Link v-else :href="route('logout')" class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Sair
                </Link>

                <PrimaryButton class="ms-4"
                    :class="{ 'opacity-25': !isFormValid || form.processing }" :disabled="!isFormValid || form.processing">
                    {{ isEditMode ? 'Alterar' : 'Registrar' }}
                </PrimaryButton>
            </div>
            <InputError class="mt-2" :message="form.errors.page" />
        </form>
    </GuestLayout>
</template>
