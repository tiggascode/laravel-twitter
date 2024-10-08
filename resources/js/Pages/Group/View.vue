<script setup>
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue'
import {useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import {computed, ref} from "vue";
import {CameraIcon, CheckCircleIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InviteUserModal from "@/Pages/Group/InviteUserModal.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import GroupForm from "@/Components/app/GroupForm.vue";
import PostList from "@/Components/app/PostList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import TabPhotos from "@/Pages/Profile/TabPhotos.vue";

const imagesForm = useForm({
    cover: null,
    thumbnail: null
})

const showNotification = ref(true);

const coverImageSrc = ref('')
const thumbnailImageSrc = ref('')
const showInviteUserModal = ref(false)
const searchKeyword = ref('')

const authUser = usePage().props.auth.user;

const isCurrentUserAdmin = computed(() => props.group.role == 'admin');
const isJoinedToGroup = computed(() => props.group.role && props.group.status === 'approved');

const props = defineProps({
    errors: Object,

    success: {
        type: String,
    },
    group: {
        type: Object
    },
    posts: Object,
    users: Array,
    requests: Array,
    photos: Array,
});


const aboutForm = useForm({
    name: usePage().props.group.name,
    auto_approval: !!parseInt(usePage().props.group.auto_approval),
    about: usePage().props.group.about.replace(/<br\s*\/?>/gi, '\n'),
})

function onCoverChange(event) {
    imagesForm.cover = event.target.files[0]
    if (imagesForm.cover) {
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.cover)
    }
}

function onThumbnailChange(event) {
    imagesForm.thumbnail = event.target.files[0]
    if (imagesForm.thumbnail) {
        const reader = new FileReader()
        reader.onload = () => {
            thumbnailImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.thumbnail)
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function resetThumbnailImage() {
    imagesForm.thumbnail = null;
    thumbnailImageSrc.value = null;
}

function submitCoverImage() {

    imagesForm.post(route('group.updateImages', props.group.slug), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true
            resetCoverImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        }
    })
}

function submitThumbnailImage() {
    imagesForm.post(route('group.updateImages', props.group.slug), {
        preserveScroll: true,
        onSuccess: () => {
            showNotification.value = true
            resetThumbnailImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        }
    })
}

function joinToGroup() {
    const form = useForm({})

    form.post(route('group.join', props.group.slug), {
        preserveScroll: true
    })
}

function approveUser(user) {

    const form = useForm({
        user_id: user.id,
        action: 'approve'
    })

    form.post(route('group.approveRequest', props.group.slug), {
        preserveScroll: true
    })
}

function rejectUser(user) {

    const form = useForm({
        user_id: user.id,
        action: 'reject'
    })

    form.post(route('group.approveRequest', props.group.slug), {
        preserveScroll: true
    })
}

function onRoleChange(user, role) {
    const form = useForm({
        user_id: user.id,
        role
    })

    form.post(route('group.changeRole', props.group.slug), {
        preserveScroll: true
    })
}

function updateGroup() {
    aboutForm.put(route('group.update', props.group.slug), {
        preserveScroll: true
    })
}

function deleteUser(user) {
    if (!window.confirm(`Are you sure you want to remove user "${user.name}" from this group?`)) {
        return false
    }
    const form = useForm({
        user_id: user.id,
    })

    form.delete(route('group.removeUser', props.group.slug), {
        preserveScroll: true
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-[768px]  mx-auto  h-full overflow-auto">
            <div
                v-show="showNotification && success"
                class="my-2 py-2 px-3 font-medium text-sm bg-emerald-600 text-white"
            >
                {{ success }}
            </div>
            <div
                v-if="errors.cover"
                class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white"
            >
                {{ errors.cover }}
            </div>


            <div class="group relative bg-white dark:bg-slate-950 dark:text-gray-100">
                <img :src="coverImageSrc || group.cover_url || '/img/No_image.png'"
                     class="w-full h-[200px] object-cover">
                <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2 ">
                    <button
                        v-if="!coverImageSrc"
                        class="bg-gray-50 hover:bg-gray-100 text-gray-800
                            py-1 px-2 text-xs flex items-center opacity-0 group-hover:opacity-100">
                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                            <path
                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"
                                stroke-linecap="round"
                                stroke-linejoin="round"/>
                        </svg>
                        Update cover image
                        <input class="absolute left-0 top-0 bottom-0 right-0 opacity-0 cursor-pointer" type="file"
                               @change="onCoverChange">
                    </button>
                    <div v-else class="flex gap-2 bg-white p-2 opacity-0  group-hover:opacity-100">
                        <button
                            class="bg-gray-50 hover:bg-gray-100 text-gray-800
                            py-1 px-2 text-xs flex items-center "
                            @click="resetCoverImage">

                            <XMarkIcon class="h-3 w-3 mr-2"/>
                            Cancel

                        </button>
                        <button
                            class="bg-gray-800 hover:bg-gray-900 text-gray-100
                            py-1 px-2 text-xs flex items-center  "
                            @click="submitCoverImage">

                            <CheckCircleIcon class="h-3 w-3 mr-2"/>
                            Submit

                        </button>
                    </div>
                </div>

                <div class="flex ">

                    <div class="flex items-center justify-center  relative group/thumbnail
                             -mt-[64px] ml-[48px]  w-[128px] h-[128px] rounded-full overflow-hidden">
                        <img :src="thumbnailImageSrc || group.thumbnail_url || '/img/default_thumbnail.jpg'"
                             class="w-full h-full object-cover rounded-full">
                        <button
                            v-if="isCurrentUserAdmin && !thumbnailImageSrc"
                            class="absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full
                                opacity-0 flex items-center justify-center group-hover/thumbnail:opacity-100">
                            <CameraIcon class="w-8 h-8 "/>
                            <input class="absolute left-0 top-0 bottom-0 right-0 opacity-0 cursor-pointer"
                                   type="file"
                                   @change="onThumbnailChange">
                        </button>
                        <div v-else-if="isCurrentUserAdmin"
                             class="absolute top-1/3 right-1/3  flex flex-col gap-2   ">
                            <button
                                class="w-7 h-7 flex items-center  justify-center bg-red-500/80
                                    text-white rounded-full"
                                @click="resetThumbnailImage">
                                <XMarkIcon class="h-5 w-5 "/>
                            </button>

                            <button
                                class="w-7 h-7 flex items-center justify-center bg-emerald-500/80
                                    text-white rounded-full"
                                @click="submitThumbnailImage">
                                <CheckCircleIcon class="h-5 w-5 "/>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center flex-1 p-4">
                        <h2 class="font-bold text-lg">{{ group.name }}</h2>

                        <PrimaryButton v-if="!authUser" :href="route('login')">
                            Login to join to this group
                        </PrimaryButton>

                        <PrimaryButton v-if="isCurrentUserAdmin"
                                       @click="showInviteUserModal = true">
                            Invite Users
                        </PrimaryButton>

                        <PrimaryButton v-if="authUser && !group.role && group.auto_approval"
                                       @click="joinToGroup">
                            Join to Group
                        </PrimaryButton>

                        <PrimaryButton v-if="authUser && !group.role && !group.auto_approval"
                                       @click="joinToGroup">
                            Request to Join
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <div class="border-t   ">
                <TabGroup>
                    <TabList class="  flex bg-white dark:bg-slate-950 dark:text-gray-100">

                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Posts"/>
                        </Tab>
                        <Tab v-if="isJoinedToGroup" v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="User"/>
                        </Tab>
                        <Tab v-if="isCurrentUserAdmin" v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Pending Requests"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Photos"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="About"/>
                        </Tab>

                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel>
                            <template v-if="posts">
                                <CreatePost :group="group"/>
                                <PostList v-if="posts.data.length" :posts="posts.data" class="flex-1"/>
                                <div v-else class="text-white py-8 text-center">
                                    There are no posts in this group, Be the first and create it
                                </div>
                            </template>
                            <div v-else class="py-8 text-center text-white">
                                You dont have permission to view these posts
                            </div>
                        </TabPanel>
                        <TabPanel v-if="isJoinedToGroup">
                            <!--                            <div class="mb-3">-->
                            <!--                                <TextInput :model-value="searchKeyword"-->
                            <!--                                           class="w-full" placeholder="Type To search "/>-->
                            <!--                            </div>-->
                            <div class="grid grid-cols-2 gap-3">
                                <UserListItem v-for="user of users"
                                              :key="user.id"
                                              :disable-role-dropdown="group.user_id == user.id"
                                              :show-role-dropdown="isCurrentUserAdmin"
                                              :user="user"
                                              class="shadow rounded-lg"
                                              @delete="deleteUser"
                                              @role-change="onRoleChange"/>
                            </div>
                        </TabPanel>
                        <TabPanel v-if="isCurrentUserAdmin" class="">
                            <div v-if="requests.length" class="grid grid-cols-2 gap-3">
                                <UserListItem v-for="user of requests"
                                              :key="user.id"
                                              :for-aprrove="true"
                                              :user="user"
                                              class="shadow rounded-lg"
                                              @approve="approveUser"
                                              @reject="rejectUser"/>
                            </div>
                            <div v-else class="py-8 text-center text-white">
                                There are no pending requests
                            </div>
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            <TabPhotos :photos="photos"/>
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            <template v-if="isCurrentUserAdmin">
                                <GroupForm :form="aboutForm"/>
                                <PrimaryButton @click="updateGroup">
                                    Submit
                                </PrimaryButton>
                            </template>
                            <div v-else class="ck-content-output" v-html="group.about">
                            </div>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
    <InviteUserModal v-model="showInviteUserModal"/>
</template>
