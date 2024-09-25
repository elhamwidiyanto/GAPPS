<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<script setup>
import { onMounted,ref,reactive,computed } from 'vue'
import { router, Head, Link, useForm } from "@inertiajs/vue3"
import { MqResponsive } from "vue3-mq";
import {
  mdiAccountKey,
  mdiPlus,
  mdiSquareEditOutline,
  mdiTrashCan,
  mdiAlertBoxOutline,
  mdiFileDocumentEdit,
  mdiAccountSwitch,
  mdiFilePdfBox,
  mdiClipboardListOutline,
  mdiWhatsapp,
  mdiCalendarMonth,
  mdiExportVariant,
  mdiFileDownloadOutline,
  mdiFileExcel,
  mdiAccountArrowUp,
  mdiArrowLeftBoldOutline,
  mdiMagnifyScan,
  mdiListBoxOutline,
  mdiHomeCircle,
  mdiBroom
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import BaseButton from "@/Components/BaseButton.vue"
import CardBox from "@/Components/CardBox.vue"
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButtons from "@/Components/BaseButtons.vue"
import NotificationBar from "@/Components/NotificationBar.vue"
import Multiselect from 'vue-multiselect'
import Pagination from "@/Components/Admin/Pagination.vue"
import Sort from "@/Components/Admin/Sort.vue"
import CardBoxModal from '@/Components/CardBoxModal.vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import PillTag from "@/Components/PillTag.vue";
import { pickBy, throttle } from 'lodash';

// axios
import axios from 'axios';

// moment
import moment from 'moment'

import { TailwindPagination } from 'laravel-vue-pagination';

const props = defineProps({
//   myRoles: {
//     type: Array,
//     default: () => ([]),
//   },
//   master_status: {
//     type: Array,
//     default: () => ([]),
//   },
//   app_url: {
//     type: String,
//     default: () => ({}),
//   },
    cleaning_ditolak: {
      type: Number,
      default: () => (0),
    },
    cleaning_diajukan: {
      type: Number,
      default: () => (0),
    },
    cleaning_disetujui: {
      type: Number,
      default: () => (0),
    },
    cleaning_dicancel: {
      type: Number,
      default: () => (0),
    },
    maintenance_ditolak: {
      type: Number,
      default: () => (0),
    },
    maintenance_diajukan: {
      type: Number,
      default: () => (0),
    },
    maintenance_disetujui: {
      type: Number,
      default: () => (0),
    },
    maintenance_dicancel: {
      type: Number,
      default: () => (0),
    },
    it_ditolak: {
      type: Number,
      default: () => (0),
    },
    it_diajukan: {
      type: Number,
      default: () => (0),
    },
    it_disetujui: {
      type: Number,
      default: () => (0),
    },
    it_dicancel: {
      type: Number,
      default: () => (0),
    },
})

onMounted(() => {
});

const steps = [
  {
    target: '.step-1',
    content: 'Harap menentukan TTD terlebih dahulu !',
  },
  {
    target: '.step-1',
    content: 'Klik tombol next untuk menentukan TTD',
    placement: 'bottom',
  }
];

const goToProfileSetting = () => {

  let url = `/admin/edit-account-info`;

    router.visit(url, {
        method: 'get',
        data: {},
        replace: false,
        preserveState: false,
        preserveScroll: false,
        only: [],
        headers: {},
        errorBag: null,
        forceFormData: false,
        onCancelToken: cancelToken => {},
        onCancel: () => {},
        onBefore: visit => {},
        onStart: visit => {},
        onProgress: progress => {},
        onSuccess: page => {},
        onError: errors => {},
        onFinish: visit => {},
    })
}

const filter = reactive({
    lock_submit: false,
    is_show_signature: false
});

</script>

<template>
  <LayoutAuthenticated>
    <Head title="Beranda" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiHomeCircle"
        title="Beranda"
        main
      >
      <BaseButtons type="justify-start lg:justify-end">

      </BaseButtons>
      </SectionTitleLineWithButton>
      <div class="stats flex flex-wrap justify-center items-center">
        <div class="stat border rounded-lg p-4 bg-current mb-5 mt-5 md:w-1/4">
          <div class="stat-figure text-primary flex justify-center items-center">
            <svg width="115px" height="115px" viewBox="-102.4 -102.4 1228.80 1228.80" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000" transform="matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"><rect x="-102.4" y="-102.4" width="1228.80" height="1228.80" rx="614.4" fill="#7ed0ec" strokewidth="0"></rect></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M928 480c-17.7 0-32 14.3-32 32 0 211.7-172.3 384-384 384-8 0-16-0.3-23.9-0.7-32.2 19.6-67.2 35.2-104.1 46.2 41.4 12.3 84.4 18.6 128 18.6 60.5 0 119.1-11.8 174.4-35.2 53.4-22.6 101.3-54.9 142.4-96 41.1-41.1 73.4-89 96-142.4 23.4-55.4 35.2-114 35.2-174.5 0-17.7-14.3-32-32-32z" fill="#3D5AFE"></path><path d="M512 128c17.7 0 32-14.3 32-32s-14.3-32-32-32c-43.8 0-86.7 6.2-128 18.5 36.9 11 71.9 26.6 104.1 46.2 7.9-0.4 15.9-0.7 23.9-0.7zM617.8 247.8l61.5-61.5-79.1-79.3-59.2 59.2c28.8 23.9 54.7 51.3 76.8 81.6zM692.5 613.2l26.3 8.8L917 423.8l-79.2-79.2-118.7 118.7-18.4-6.2c2.2 18 3.3 36.3 3.3 54.8 0 34.9-4 68.8-11.5 101.3zM393.5 877.4c-16.8-5.4-34.9 3.8-40.3 20.6s3.8 34.9 20.6 40.3c3.4 1.1 6.8 2.1 10.2 3.2 36.9-11 71.8-26.6 104.1-46.2-32.3-2-64-8-94.6-17.9z" fill="#FFEA00"></path><path d="M195.2 195.2c-41.1 41.1-73.4 89-96 142.4C75.8 392.9 64 451.5 64 512c0 47.2 7.3 93.8 21.8 138.3 4.4 13.5 16.9 22.1 30.4 22.1 3.3 0 6.6-0.5 9.9-1.6 16.8-5.5 26-23.5 20.6-40.3-12.4-38.1-18.6-78-18.6-118.5 0-203.7 159.5-370.9 360.1-383.3a446.34 446.34 0 0 0-104.1-46.2c-15.7 4.7-31.2 10.2-46.4 16.7-53.4 22.6-101.4 54.9-142.5 96zM441.6 423.8l-237.8 238c-43.7 43.7-43.7 114.7 0 158.4s114.7 43.7 158.4 0l237.9-237.9 92.4 30.9C700 580.7 704 546.8 704 512c0-18.6-1.1-36.9-3.3-54.8l-100.3-33.6-39.7-118.7 57.1-57.1c-22.1-30.3-48-57.7-76.9-81.5L402 305.2l39.6 118.6zM283 701.4c10.9-10.9 28.7-10.9 39.6 0 10.9 10.9 10.9 28.7 0 39.6L283 780.6c-10.9 10.9-28.7 10.9-39.6 0-10.9-10.9-10.9-28.7 0-39.6" fill="#3D5AFE"></path></g></svg>
          </div>
          <div class="stat-title text-center mt-3 font-mono text-lg text-black">Maintenance</div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#055aba" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Diajukan</span>
              <span class="text-black text-2xl">{{ props.maintenance_diajukan }}</span>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#22bb33" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Disetujui</span>
              <span class="text-black text-2xl">{{ props.maintenance_disetujui }}</span>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#bb2124" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Ditolak</span>
              <span class="text-black text-2xl">{{ props.maintenance_ditolak }}</span>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#ffc302" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm5.757-1a1 1 0 1 0 0 2h8.486a1 1 0 1 0 0-2H7.757Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Dibatalkan</span>
              <span class="text-black text-2xl">{{ props.maintenance_dicancel }}</span>
            </div>
        </div>

        <div class="stat border rounded-lg p-4 mr-10 ml-10 bg-current md:w-1/4  mb-5 mt-5">
          <div class="stat-figure text-secondary flex justify-center items-center">
            <svg width="115px" height="115px" viewBox="-102.4 -102.4 1228.80 1228.80" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#3D5AFE" stroke="#3D5AFE"><g id="SVGRepo_bgCarrier" stroke-width="0"><rect x="-102.4" y="-102.4" width="1228.80" height="1228.80" rx="614.4" fill="#7ed0ec" strokewidth="0"></rect></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M969.536 402.944a22.4 22.4 0 0 0-22.336 22.08V273.088a22.4 22.4 0 0 0 44.736-0.128h0.064V160a22.4 22.4 0 0 0-22.4-22.4H54.4a22.528 22.528 0 0 0-22.4 22.4v604.608c0 12.288 10.112 22.4 22.4 22.4h915.2a22.4 22.4 0 0 0 22.4-22.4V425.344a22.4 22.4 0 0 0-22.464-22.4z" fill=""></path><path d="M969.6 328m-22.4 0a22.4 22.4 0 1 0 44.8 0 22.4 22.4 0 1 0-44.8 0Z" fill=""></path><path d="M77.44 631.168a0.64 0.64 0 0 1-0.64-0.64V189.376a0.64 0.64 0 0 1 0.64-0.64h869.184c0.256 0 0.64 0.256 0.64 0.64v441.152c0 0.32-0.384 0.64-0.64 0.64H77.44z" fill="#FFEA00"></path><path d="M77.44 740.032a0.64 0.64 0 0 1-0.64-0.64v-69.12a0.64 0.64 0 0 1 0.64-0.64h869.184c0.256 0 0.64 0.32 0.64 0.64v69.12c0 0.32-0.384 0.64-0.64 0.64H77.44z" fill="#FFEA00"></path><path d="M76.8 631.168h870.464v44.8H76.8zM637.12 875.776H386.816l19.264-88.768h211.904z" fill=""></path><path d="M431.68 875.776l19.2-88.768h122.368l19.136 88.768z" fill="#FFEA00"></path><path d="M721.984 888a16 16 0 0 1-16 16H318.016a16 16 0 0 1 0-32h388.032a16 16 0 0 1 15.936 16z" fill=""></path><path d="M512 706.688m-18.176 0a18.176 18.176 0 1 0 36.352 0 18.176 18.176 0 1 0-36.352 0Z" fill=""></path><path d="M366.272 445.76a22.464 22.464 0 0 1-31.68 0 22.464 22.464 0 0 1 0-31.68l134.272-134.272a22.464 22.464 0 0 1 31.68 0 22.464 22.464 0 0 1 0 31.68L366.272 445.76z" fill="#536DFE"></path><path d="M407.424 540.16a22.464 22.464 0 0 1-31.68 0 22.528 22.528 0 0 1 0-31.744l67.328-67.264a22.464 22.464 0 0 1 31.68 0 22.464 22.464 0 0 1 0 31.68L407.424 540.16z" fill="#536DFE"></path></g></svg>
          </div>
          <div class="stat-title text-center mt-3 font-mono text-lg text-black">IT</div>
          <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#055aba" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Diajukan</span>
              <span class="text-black text-2xl">{{ props.it_diajukan }}</span>
            </div>
          <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#22bb33" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Disetujui</span>
              <span class="text-black text-2xl">{{ props.it_disetujui }}</span>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#bb2124" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Ditolak</span>
              <span class="text-black text-2xl">{{ props.it_ditolak }}</span>             
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#ffc302" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm5.757-1a1 1 0 1 0 0 2h8.486a1 1 0 1 0 0-2H7.757Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Dibatalkan</span>
              <span class="text-black text-2xl">{{ props.it_dicancel }}</span>
            </div>      
        </div>

        <div class="stat border rounded-lg p-4 bg-current md:w-1/4 mb-5 mt-5">
          <div class="stat-figure text-secondary flex justify-center items-center">
            <svg height="115px" width="115px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="-49.6 -49.6 595.21 595.21" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"><rect x="-49.6" y="-49.6" width="595.21" height="595.21" rx="297.605" fill="#7ed0ec" strokewidth="0"></rect></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path style="fill:#3D5AFE;" d="M372.976,344.011c6.4-6.4,6.4-16,0-21.6l-58.4-58.4c0,0,0,0,0.8-0.8c4.8-4.8,4.8-12,0-16.8 l-15.2-14.4c36-18.4,88-57.6,134.4-104.8c74.4-74.4,67.2-96.8,49.6-115.2c-18.4-18.4-40.8-24.8-115.2,49.6 c-46.4,46.4-86.4,98.4-104.8,134.4l-15.2-15.2c-4.8-4.8-12-4.8-16.8,0c0,0,0,0-0.8,0.8l-58.4-58.4c-6.4-6.4-16-6.4-21.6,0 l-146.4,93.6c-6.4,6.4-6.4,16,0,21.6l252.8,252.8c6.4,6.4,16,6.4,21.6,0L372.976,344.011z M439.376,56.811c-7.2-6.4-7.2-17.6,0-24.8 c6.4-6.4,17.6-6.4,24.8,0c7.2,6.4,6.4,17.6,0,24.8C456.976,64.011,445.776,64.011,439.376,56.811z"></path> <path style="fill:#FFEA00;" d="M264.176,196.011l-15.2-15.2c-4.8-4.8-12-4.8-16.8,0c0,0,0,0-0.8,0.8l-58.4-58.4 c-6.4-6.4-16-6.4-21.6,0l-146.4,93.6c-6.4,6.4-6.4,16,0,21.6l252.8,252.8c6.4,6.4,16,6.4,21.6,0l93.6-147.2c6.4-6.4,6.4-16,0-21.6 l-58.4-58.4c0,0,0,0,0.8-0.8c4.8-4.8,4.8-12,0-16.8l-15.2-14.4c36-18.4,88-57.6,134.4-104.8"></path> <path style="fill:#3D5AFE;" d="M338.576,350.411l-192.8-192.8l-144,76c0.8,1.6,1.6,3.2,3.2,4.8l252.8,252.8c1.6,1.6,3.2,2.4,4.8,3.2 L338.576,350.411z"></path> <path style="fill:#0F58D8;" d="M332.976,356.011l-192.8-192.8l-138.4,70.4c0.8,1.6,1.6,3.2,3.2,4.8l252.8,252.8 c1.6,1.6,3.2,2.4,4.8,3.2L332.976,356.011z"></path> <polygon style="fill:#0650BA;" points="307.376,406.411 332.976,356.011 140.176,163.211 89.776,188.811 "></polygon> </g></svg>
          </div>
          <div class="stat-title text-center mt-3 font-mono text-lg text-black">Cleaning</div>
          <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#055aba" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Diajukan</span>
              <span class="text-black text-2xl">{{ props.cleaning_diajukan }}</span>
            </div>
          <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#22bb33" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Disetujui</span>
              <span class="text-black text-2xl">{{ props.cleaning_disetujui }}</span>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#bb2124" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Ditolak</span>
              <span class="text-black text-2xl">{{ props.cleaning_ditolak }}</span>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <svg class="text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#ffc302" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm5.757-1a1 1 0 1 0 0 2h8.486a1 1 0 1 0 0-2H7.757Z" clip-rule="evenodd"/>
              </svg>
              <span class="text-black text-xl font-mono mr-5 ml-5">Dibatalkan</span>
              <span class="text-black text-2xl">{{ props.cleaning_dicancel }}</span>
            </div>
        </div>
      </div>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<style scoped>
    .dateFilter {
        width: 100%;
        height: 38px;
    }
</style>
