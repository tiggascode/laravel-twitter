<script setup>
defineProps({
    preview: Object,
    url: String
})
</script>

<template>
    <a v-if="preview && preview.title" :href="url"
       class="mt-4 block border border-indigo-200 bg-indigo-50"
       target="_blank">
        <img :src="preview.image" class="max-w-full"/>
        <div class="p-2">
            <h3 class=" font-semibold">{{ preview.title }}</h3>
            <p class="text-sm m-none">{{ preview.description }}</p>
        </div>
    </a>
    <a v-else-if="url" :href="url"
       class="mt-4 block border border-indigo-200 bg-indigo-50"
       target="_blank">
        <div class="p-2">
            {{ url }}
        </div>
    </a>
</template>

<style scoped>

</style>
