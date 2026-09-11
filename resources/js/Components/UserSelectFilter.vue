<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    usuarios: {
        type: Array,
        required: true,
        default: () => []
    },
    modelValue: {
        type: Array,
        required: true,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const isDropdownOpen = ref(false);

const isUsuarioSelecionado = (id) => {
    return props.modelValue.some(uId => String(uId) === String(id));
};

const toggleUsuario = (id) => {
    const stringId = String(id);
    const index = props.modelValue.findIndex(uId => String(uId) === stringId);
    let updated = [...props.modelValue];

    if (index === -1) {
        updated.push(id);
    } else {
        updated.splice(index, 1);
    }
    emit('update:modelValue', updated);
};

const isAllSelected = computed(() => {
    if (!props.usuarios || props.usuarios.length === 0) return false;
    return props.modelValue.length === props.usuarios.length;
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        emit('update:modelValue', []);
    } else {
        emit('update:modelValue', props.usuarios.map(u => u.id));
    }
};

const labelFiltro = computed(() => {
    const count = props.modelValue.length;
    if (count === 0) return 'Nenhum selecionado';
    if (count === props.usuarios.length) return 'Todos os Usuários';
    if (count === 1) return '1 Usuário';
    return `${count} Usuários`;
});
</script>

<template>
    <div v-if="usuarios.length > 0" class="relative">
        <div class="flex items-center gap-3 p-2.5 px-4 rounded-2xl bg-white dark:bg-[#0F172A] border border-slate-200/80 dark:border-slate-800 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_40px_-15px_rgba(0,0,0,0.7)] transition-all">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Filtrar por Nome:
            </span>
            
            <button
                type="button"
                @click="isDropdownOpen = !isDropdownOpen"
                class="flex items-center justify-between gap-2 min-w-[160px] rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 text-xs font-medium py-1.5 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <span class="truncate">{{ labelFiltro }}</span>
                <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': isDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <!-- Dropdown com Checkboxes -->
        <div
            v-if="isDropdownOpen"
            class="absolute right-0 z-50 mt-2 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-2 space-y-1 max-h-60 overflow-y-auto"
        >
            <label 
                @click.prevent="toggleSelectAll"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-indigo-50 dark:hover:bg-slate-800/80 cursor-pointer transition-colors"
            >
                <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @click.stop
                    @change="toggleSelectAll"
                    class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                />
                <span class="ml-2.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                    Selecionar Todos
                </span>
            </label>

            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

            <label 
                v-for="usuario in usuarios" 
                :key="usuario.id"
                @click.prevent="toggleUsuario(usuario.id)"
                class="flex items-center px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors"
            >
                <input
                    type="checkbox"
                    :checked="isUsuarioSelecionado(usuario.id)"
                    @click.stop
                    @change="toggleUsuario(usuario.id)"
                    class="rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                />
                <span class="ml-2.5 text-xs text-slate-700 dark:text-slate-300 truncate">
                    {{ usuario.name }}
                </span>
            </label>
        </div>
    </div>
</template>
