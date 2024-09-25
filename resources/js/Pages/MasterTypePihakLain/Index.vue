<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<script setup>
import { inject,onMounted,ref,reactive,computed } from 'vue'
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
  mdiEmailFast
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
  myRoles: {
    type: Array,
    default: () => ([]),
  },
  master_status: {
    type: Array,
    default: () => ([]),
  },
  user_id: {
    type: Number,
    default: () => (0),
  },
  app_url: {
    type: String,
    default: () => (''),
  },
})

onMounted(() => {
    get_json_index();
    check_signature();
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

const check_signature = () => {
  //HTTP request
  axios.get(`/sirkuler/createlist/check_signature`, {
    })
    .then(response => {

        if(response.data.data == 0){
          filter.is_show_signature = true;
        }

    }).catch(function (error) {
        if (error.response) {
            console.log(error.response)
        }
    });
}

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

const format = (date) => {
    
    const day =  moment(date).format("DD");
    const month = moment(date).format("MMM");
    const year =  moment(date).format("YYYY");

    const hour = moment(date).format("H");
    const minute = moment(date).format("m");

    return `${day}/${month}/${year}`;
}

const formatDateTime = (date) => {
    const day =  moment(date).format("DD");
    const month = moment(date).format("MMM");
    const year =  moment(date).format("YYYY");

    const hour =  moment(date).format("HH");
    const minute =  moment(date).format("mm");

    return `${day}/${month}/${year} ${hour}:${minute}`;
}

const master_type_pihak_lain = ref([]);

const get_json_index = async (page = 1, field = '') => {

    if(field){
        filter.field_master_type_pihak_lain_list = field;
        filter.direction_master_type_pihak_lain_list = filter.direction_master_type_pihak_lain_list == 'asc' ? 'desc' : 'asc';
    }

    //HTTP request
    axios.get(`/sirkuler/master_type_pihak_lain/json_index?page=${page}&field=${filter.field_master_type_pihak_lain_list}&direction=${filter.direction_master_type_pihak_lain_list}&search=${filter.search_master_type_pihak_lain_list}&search_status=${(filter.search_status) ? filter.search_status.id : '' }`, {
    })
    .then(response => {

      master_type_pihak_lain.value = response.data.data;

    }).catch(function (error) {
        if (error.response) {
            console.log(error.response)
        }
    });
}

const filter = reactive({
  field_master_type_pihak_lain_list: '',
  direction_master_type_pihak_lain_list: '',
  search_master_type_pihak_lain_list: '',
  search_status: '',

  sirkuler_id: '',

  is_show_signature: false,  
});

const formDelete = useForm({});

const isModalDangerActive = ref(false);
const idDeleteModal = ref();

function destroy(id) {
    // formDelete.delete(route("job_costing.destroy", id))
}

function openModalDanger(value) {
  isModalDangerActive.value = true;
  idDeleteModal.value = value;
}

const swal = inject('$swal');
const disabled_resend = ref(false);
const buttonLoading = ref(false);
const timeout = ref(null);

const beforeDestroy = () => {
    // clear the timeout before the component is destroyed
    clearTimeout(timeout)
}

</script>

<template>
  <LayoutAuthenticated>
    <Head title="List Master Type Pihak Lain" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiFileDocumentEdit"
        title="List Master Type Pihak Lain"
        main
      >
      <BaseButtons type="justify-start lg:justify-end">

        <BaseButton
          :route-name="route('master_pihak_lain.create')"
          :icon="mdiPlus"
          label="Tambah Pihak Lain"
          color="success"
          rounded-full
          small
        />

      </BaseButtons>
      </SectionTitleLineWithButton>
      <NotificationBar
        v-if="$page.props.flash.message"
        color="success"
        :icon="mdiAlertBoxOutline"
      >
        {{ $page.props.flash.message }}
      </NotificationBar>
      <CardBox class="mb-6" has-table>
        <form @submit.prevent="get_json_index()">
          <div class="-mx-3 md:flex mb-6 p-3">
            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
              <FormControl
                    v-model="filter.search_master_pihak_lain_list"
                    type="text"
                    placeholder="Search..."
                >
              </FormControl>
            </div>
            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
              <Multiselect
                    v-model="filter.search_status"
                    :options="master_status"
                    placeholder="Pilih Status..."
                    label="nama"
                    track-by="id"
                    >
                    <template v-slot:noResult>
                        Oops! Status tidak ditemukan.
                    </template>
                </Multiselect>
            </div>
            <!-- <div class="md:w-1/2 px-3 mb-6 md:mb-0">
              <VueDatePicker v-model="form.search_tanggal" :format="format" class="dateFilter" :enable-time-picker="false"></VueDatePicker>
            </div>
             -->
            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
              <button class="dy-btn dy-btn-primary ml-1"><span class="text-slate-50">Search</span></button>
            </div>
          </div>
        </form>
      </CardBox>
      
      <MqResponsive target="md-">
        <CardBox class="mb-6" has-table>
          <table class="table table-responsive px-auto text-slate-950 dark:text-slate-50">
            <tr>
              <th>
                <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','is_active')">
                      <span
                      class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                      >Status</span
                      >
                      <div class="flex flex-col">
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'is_active' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                      >
                          <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                      </svg>
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'is_active' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                      >
                          <path
                          d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                          fill="lightgray"
                          />
                      </svg>
                      </div>
                </div>
              </th>
              <th>
                <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','code')">
                      <span
                      class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                      >Code</span
                      >
                      <div class="flex flex-col">
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'code' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                      >
                          <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                      </svg>
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'code' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                      >
                          <path
                          d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                          fill="lightgray"
                          />
                      </svg>
                      </div>
                </div>
              </th>
              <th>
                <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','name')">
                      <span
                      class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                      >Name</span
                      >
                      <div class="flex flex-col">
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'publish_code' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                      >
                          <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                      </svg>
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'publish_code' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                      >
                          <path
                          d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                          fill="lightgray"
                          />
                      </svg>
                      </div>
                </div>
              </th>
              <th>
                <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','created_at')">
                      <span
                      class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                      >Tanggal Dibuat</span
                      >
                      <div class="flex flex-col">
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'created_at' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                      >
                          <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                      </svg>
                      <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_master_pihak_lain_list == 'created_at' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                      >
                          <path
                          d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                          fill="lightgray"
                          />
                      </svg>
                      </div>
                </div>
              </th>
            </tr>
          </table>
        </CardBox>
      </MqResponsive>

      <CardBox class="mb-6" has-table>

        <!-- signature modal -->
        <CardBoxModal
          v-model="filter.is_show_signature"
          large-title="Harap menentukan TTD terlebih dahulu!"
          button="danger"
          has-cancel
          buttonLabel="Lanjutkan"
          :buttonCancelSmall="true"
          :buttonConfirmSmall="true"
          :colorButtonCancel="`danger`"
          :colorButtonConfirm="`success`"
          :hasConfirm="false"
          :hasCancel="false"
          @confirm="goToProfileSetting()"
        >

        <div>
          <BaseButton
            :route-name="`/admin/edit-account-info`"
            color="info"
            label="Next"
            class="step-1"
          />
        </div>

        <VTour :steps="steps" autoStart />

        </CardBoxModal>

        <!-- delete modal -->
        <CardBoxModal
          v-model="isModalDangerActive"
          large-title="Please confirm"
          button="danger"
          has-cancel
          @confirm="destroy(idDeleteModal)"
        >
          <p>Are you sure ? </p>
        </CardBoxModal>

        <div class="overflow-x-auto">
          <table class="table table-responsive px-auto text-slate-950 dark:text-slate-50">
            <thead>
                <tr>
                    <th>
                        <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','is_active')">
                            <span
                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                            >Status</span
                            >
                            <div class="flex flex-col">
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'is_active' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                            >
                                <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                            </svg>
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'is_active' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                            >
                                <path
                                d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                                fill="lightgray"
                                />
                            </svg>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','code')">
                            <span
                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                            >Code</span
                            >
                            <div class="flex flex-col">
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'code' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                            >
                                <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                            </svg>
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'code' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                            >
                                <path
                                d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                                fill="lightgray"
                                />
                            </svg>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','name')">
                            <span
                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                            >Name</span
                            >
                            <div class="flex flex-col">
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'publish_code' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                            >
                                <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                            </svg>
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'publish_code' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                            >
                                <path
                                d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                                fill="lightgray"
                                />
                            </svg>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="flex items-center gap-4 cursor-pointer m-1" @click="get_json_index('','created_at')">
                            <span
                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                            >Tanggal Dibuat</span
                            >
                            <div class="flex flex-col">
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'created_at' && filter.direction_master_pihak_lain_list == 'asc') ? true : false"
                            >
                                <path d="M7.5 3L15 11H0L7.5 3Z" fill="lightgray" />
                            </svg>
                            <svg
                                class="inline-block"
                                xmlns="http://www.w3.org/2000/svg"
                                width="15px"
                                height="15px"
                                viewBox="0 0 15 15"
                                fill="none"
                                v-if="(filter.field_master_pihak_lain_list == 'created_at' && filter.direction_master_pihak_lain_list == 'desc') ? true : false"
                            >
                                <path
                                d="M7.49988 12L-0.00012207 4L14.9999 4L7.49988 12Z"
                                fill="lightgray"
                                />
                            </svg>
                            </div>
                        </div>
                    </th>
                    <th>
                        #
                    </th>
                </tr>
            </thead>
            
            <tbody>
              <tr v-for="(data, index) in master_pihak_lain.data" :key="index">
                <td data-label="Status">
                   <PillTag
                        :type="`danger`"
                        :text="`Inactive`"
                        :outline="false"
                        :small="true"
                        v-if="data.is_active == 0"
                    />
                    <PillTag
                        :type="`success`"
                        :text="`Active`"
                        :outline="false"
                        :small="true"
                        v-if="data.is_active == 1"
                    />
                </td>
                <td data-label="Code">
                  {{ data.code }}
                </td>
                <td data-label="Name">
                  {{ data.name }}
                </td>
                <td data-label="Tgl Dibuat">
                  {{ format(data.created_at) }}
                </td>
                <td
                  class="before:hidden lg:w-1 whitespace-nowrap"
                >
                  <BaseButtons type="justify-start lg:justify-end" no-wrap>
                    <BaseButton
                      :route-name="`/sirkuler/master_pihak_lain/${data.id}/edit`"
                      color="info"
                      :icon="mdiSquareEditOutline"
                      small
                    />
                  </BaseButtons>
                </td>
              </tr>
            </tbody>
          </table>
          <span>
            Total : {{ master_pihak_lain.total }}
            </span>
        </div>
        
        <div class="overflow-x-scroll w-96" v-if="master_pihak_lain.total > 5">
        <TailwindPagination
            :data="master_pihak_lain"
            :number="2"
            :limit="5"
            @pagination-change-page="get_json_index"
        />
        </div>
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<style scoped>
    .dateFilter {
        width: 100%;
        height: 38px;
    }
</style>
