<script setup>

import GroupItem from "@/Components/app/GroupItem.vue";
import {ref} from "vue";
import GroupModal from "@/Components/app/GroupModal.vue";

const showNewGroupModal = ref(false);

const props = defineProps({
    groups: Array,
})

function onGroupCreate(group) {
    props.groups.unshift(group)
}
</script>

<template>
    <button
        class="text-sm mt-4 bg-indigo-500 hover:bg-indigo-600 text-white rounded py-1 px-2"
        @click="showNewGroupModal = true">
        new group
    </button>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            You are not joined to any groups
        </div>
        <div v-else>
            <GroupItem v-for="group of groups" :group="group"/>

        </div>
    </div>
    <GroupModal v-model="showNewGroupModal" @create="onGroupCreate"/>

</template>

<style scoped>

</style>
