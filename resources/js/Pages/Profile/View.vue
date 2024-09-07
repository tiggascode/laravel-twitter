<script setup>
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue'
import {useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import {computed, ref} from "vue";
import {CameraIcon, CheckCircleIcon, XMarkIcon} from '@heroicons/vue/24/solid'
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import PostList from "@/Components/app/PostList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TabPhotos from "@/Pages/Profile/TabPhotos.vue";

const imagesForm = useForm({
    cover: null,
    avatar: null
})

const showNotification = ref(true);

const coverImageSrc = ref('')
const avatarImageSrc = ref('')

const searchFollowersKeyword = ref('')
const searchFollowingsKeyword = ref('')

const authUser = usePage().props.auth.user;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id)


const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    success: {
        type: String,
    },
    user: {
        type: Object
    },
    isCurrentUserFollower: Boolean,
    followerCount: Number,
    posts: Object,
    followers: Array,
    followings: Array,
    photos: Array,
});


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

function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0]
    if (imagesForm.avatar) {
        const reader = new FileReader()
        reader.onload = () => {
            avatarImageSrc.value = reader.result;
        }
        reader.readAsDataURL(imagesForm.avatar)
    }
}

function resetCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function resetAvatarImage() {
    imagesForm.avatar = null;
    avatarImageSrc.value = null;
}

function submitCoverImage() {
    imagesForm.post(route('profile.updateImages'), {
        onSuccess: (user) => {
            showNotification.value = true
            resetCoverImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        }
    })
}

function submitAvatarImage() {
    imagesForm.post(route('profile.updateImages'), {
        onSuccess: (user) => {
            showNotification.value = true
            resetAvatarImage()
            setTimeout(() => {
                showNotification.value = false;
            }, 3000)
        }
    })
}

function followUser() {
    const form = useForm({
        follow: !props.isCurrentUserFollower,
    })

    form.post(route('user.follow', props.user.id), {
        preserveScroll: true,
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
            <div class="group relative bg-white dark:bg-slate-950 dark:text-gray-100 ">
                <img :src="coverImageSrc || user.cover_url || '/img/default_cover.jpg'"
                     class="w-full h-[200px] object-cover">
                <div v-if="isMyProfile" class="absolute top-2 right-2 ">
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

                    <div class="flex items-center justify-center  relative group/avatar
                             -mt-[64px] ml-[48px]  w-[128px] h-[128px] rounded-full overflow-hidden">
                        <img :src="avatarImageSrc || user.avatar_url || '/img/default_avatar.jpg'"
                             class="w-full h-full object-cover rounded-full">
                        <button
                            v-if="!avatarImageSrc && isMyProfile"
                            class="absolute left-0 top-0 right-0 bottom-0 bg-black/50 text-gray-200 rounded-full
                                opacity-0 flex items-center justify-center group-hover/avatar:opacity-100">
                            <CameraIcon v-if="isMyProfile" class="w-8 h-8 "/>
                            <input v-if="isMyProfile"
                                   class="absolute left-0 top-0 bottom-0 right-0 opacity-0 cursor-pointer"
                                   type="file"
                                   @change="onAvatarChange">
                        </button>
                        <div v-else-if="isMyProfile"
                             class="absolute top-1/3 right-1/3  flex flex-col gap-2   ">
                            <button
                                class="w-7 h-7 flex items-center  justify-center bg-red-500/80
                                    text-white rounded-full"
                                @click="resetAvatarImage">
                                <XMarkIcon class="h-5 w-5 "/>
                            </button>

                            <button
                                class="w-7 h-7 flex items-center justify-center bg-emerald-500/80
                                    text-white rounded-full"
                                @click="submitAvatarImage">
                                <CheckCircleIcon class="h-5 w-5 "/>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center flex-1 p-4">
                        <div>
                            <h2 class="font-bold text-lg">{{ user.name }}</h2>
                            <p class="text-xs text-gray-500">{{ followerCount }} followers </p>
                        </div>
                        <div v-if="!isMyProfile">
                            <PrimaryButton v-if="!isCurrentUserFollower" @click="followUser">
                                Follow User
                            </PrimaryButton>
                            <DangerButton v-else @click="followUser">
                                Unfollow User
                            </DangerButton>
                        </div>
                    </div>
                </div>

            </div>

            <div class="border-t ">
                <TabGroup>
                    <TabList class="  flex bg-white  dark:bg-slate-950 dark:text-gray-100 ">

                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Posts"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Followers"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Followings"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="Photos"/>
                        </Tab>
                        <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                            <TabItem :selected="selected" text="My Profile"/>
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel class="">
                            <template v-if="posts">
                                <CreatePost v-if="isMyProfile"/>
                                <PostList :posts="posts.data" class="flex-1"/>
                            </template>
                            <div v-else class="py-8 text-center text-white">
                                You dont have permission to view these posts
                            </div>
                        </TabPanel>
                        <TabPanel class="bg-white p-3  dark:bg-slate-800 shadow">
                            <!--                            <div class="mb-3">-->
                            <!--                                <TextInput :model-value="searchFollowersKeyword"-->
                            <!--                                           class="w-full" placeholder="Type To search "/>-->
                            <!--                            </div>-->
                            <div v-if="followers.length" class="grid grid-cols-2 gap-3">

                                <UserListItem v-for="user of followers"
                                              :key="user.id"
                                              :user="user"
                                              class="shadow rounded-lg"/>
                            </div>
                            <div v-else class="text-center py-8 text-white">
                                User does not have followers
                            </div>
                        </TabPanel>
                        <TabPanel class="bg-white p-3  dark:bg-slate-800 shadow">
                            <!--                            <div class="mb-3">-->
                            <!--                                <TextInput :model-value="searchFollowingsKeyword"-->
                            <!--                                           class="w-full" placeholder="Type To search "/>-->
                            <!--                            </div>-->
                            <div v-if="followings.length" class="grid grid-cols-2 gap-3">

                                <UserListItem v-for="user of followings"
                                              :key="user.id"
                                              :user="user"
                                              class="shadow rounded-lg"/>
                            </div>
                            <div v-else class="text-center  dark:bg-slate-800 py-8 text-white">
                                The user is not following to anybody
                            </div>
                        </TabPanel>
                        <TabPanel class="bg-white p-3  dark:bg-slate-800 shadow">
                            <TabPhotos :photos="photos"/>
                        </TabPanel>
                        <TabPanel v-if="isMyProfile">
                            <Edit :must-verify-email="mustVerifyEmail" :status="status"/>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>

</template>
