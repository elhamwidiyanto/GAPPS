<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style src="../../../css/content-styles.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed, nextTick } from 'vue'
import { router, Head, Link, useForm } from "@inertiajs/vue3"
import {
  mdiAccountKey,
  mdiArrowLeftBoldOutline,
  mdiFileDocumentEdit,
  mdiAccountSwitch,
  mdiTrashCanOutline,
  mdiClipboardListOutline,
  mdiStateMachine
} from "@mdi/js"
import LayoutGuest from '@/Layouts/LayoutGuest.vue'
import SectionMain from "@/Components/SectionMain.vue"
import SectionFullScreen from '@/Components/SectionFullScreen.vue'
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import CardBox from "@/Components/CardBox.vue"
import FormField from '@/Components/FormField.vue'
import FormControl from '@/Components/FormControl.vue'
import BaseButton from '@/Components/BaseButton.vue'
import BaseButtons from '@/Components/BaseButtons.vue'
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import CardBoxModal from '@/Components/CardBoxModal.vue';
// import FormFilePicker from "@/components/FormFilePicker.vue";
// import BaseDivider from "@/components/BaseDivider.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import '@vuepic/vue-datepicker/dist/main.css'
import Multiselect from 'vue-multiselect'
import moment from 'moment'
import useNumberFormat from '../../Helpers/numberFormat';
import PillTag from "@/Components/PillTag.vue";

//import axios
import axios from 'axios';
import { TailwindPagination } from 'laravel-vue-pagination';

const props = defineProps({
  ticket_request_id: {
    type: String,
    default: () => (''),
  },
  sirkuler_header: {
    type: Array,
    default: () => ([]),
  },
})

const { numberFormat } = useNumberFormat();

onMounted(() => {

});

const filter = reactive({
  field_detail_sirkuler_list: '',
  direction_detail_sirkuler_list: '',
  search_detail_sirkuler_list: '',
  sirkuler_id: '',
});

const getCatatanHistory = (value) => {

    filter.sirkuler_id = value.id;

    //HTTP request
    axios.get(`/sirkuler/tracking/catatan/${value.id}`)
    .then(response => {
        
        form.sirkuler_header = response.data.data_header;
        form.catatans = response.data.data;

    }).catch(function (error) {
        
        if (error.response) {
            console.log(error.response)
        }

    });
}

const detail_sirkulers = ref([]);

const getDetailSirkuler = async (page = 1, field = '') => {

    // &tanggal_dari=${ (form.date_schedule_from) ? moment(form.date_schedule_from).format("YYYY-MM-DD") : moment().format("YYYY-MM-DD") }&tanggal_sampai=${ (form.date_schedule_to) ? moment(form.date_schedule_to).format("YYYY-MM-DD") : moment().format("YYYY-MM-DD") }&status=${ (form.status) ? JSON.stringify(form.status) : '' }

    if(field){
        filter.field_detail_sirkuler_list = field;
        filter.direction_detail_sirkuler_list = filter.direction_detail_sirkuler_list == 'asc' ? 'desc' : 'asc';
    }

    //HTTP request
    axios.get(`/sirkuler/tracking/get_detail_sirkuler?page=${page}&field=${filter.field_detail_sirkuler_list}&direction=${filter.direction_detail_sirkuler_list}&search=${filter.search_detail_sirkuler_list}&sirkuler_id=${filter.sirkuler_id}`, {
    })
    .then(response => {

        detail_sirkulers.value = response.data.data;

    }).catch(function (error) {
        if (error.response) {
            console.log(error.response)
        }
    });
}

const showModalDetailSirkulerVal = ref(false);

const showModalDetailSirkuler = (id) => {

    showModalDetailSirkulerVal.value = true;
    filter.sirkuler_id = id;

    getDetailSirkuler();

}

const format = (date) => {
    const day = date.getDate();
    const month = moment(date).format("MMM");
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
}

const formatDateTime = (date) => {
    const day = moment(date).format('DD');
    const month = moment(date).format("MM");
    const year = moment(date).format("YYYY");

    const hour = moment(date).format("HH");
    const minute = moment(date).format("mm");
    const second = moment(date).format("ss");

    return `${day}/${month}/${year} ${hour}:${minute}:${second}`;
}

const form = reactive({
  catatan: '',
  catatans: [],
  no_ticket: [],
  sirkuler_header: '',
});

</script>

<template>
  <LayoutGuest>
    <Head title="Tracking Sirkuler" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiFileDocumentEdit"
        title="Tracking Sirkuler"
        main
      >
        <span></span>
      </SectionTitleLineWithButton>
      <CardBox>
        
        <!-- detail sirkuler -->
        <CardBoxModal
          v-model="showModalDetailSirkulerVal"
          large-title="Detail Approval"
          button="info"
          buttonLabel="Pilih"
          has-cancel
          :buttonCancelSmall="true"
          :colorButtonCancel="`danger`"
          :hasConfirm="false"
        >
          <div class="max-md:overflow-y-scroll max-md:h-52">
            <table class="table table-responsive px-auto text-gray-950 dark:text-slate-50">
              <thead>
                <tr>
                  <th>
                    <div class="flex items-center gap-4" @click="getDetailSirkuler('','status')">
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
                          v-if="(filter.field_detail_sirkuler_list == 'status' && filter.direction_detail_sirkuler_list == 'asc') ? true : false"
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
                          v-if="(filter.field_detail_sirkuler_list == 'status' && filter.direction_detail_sirkuler_list == 'desc') ? true : false"
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
                    <div class="flex items-center gap-4" @click="getDetailSirkuler('','name')">
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
                          v-if="(filter.field_detail_sirkuler_list == 'name' && filter.direction_detail_sirkuler_list == 'asc') ? true : false"
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
                          v-if="(filter.field_detail_sirkuler_list == 'name' && filter.direction_detail_sirkuler_list == 'desc') ? true : false"
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
                    <div class="flex items-center gap-4" @click="getDetailSirkuler('','position')">
                      <span
                        class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                        >Position</span
                      >
                      <div class="flex flex-col">
                        <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_detail_sirkuler_list == 'position' && filter.direction_detail_sirkuler_list == 'asc') ? true : false"
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
                          v-if="(filter.field_detail_sirkuler_list == 'position' && filter.direction_detail_sirkuler_list == 'desc') ? true : false"
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
                    <div class="flex items-center gap-4" @click="getDetailSirkuler('','department_name')">
                      <span
                        class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                        >Department</span
                      >
                      <div class="flex flex-col">
                        <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_detail_sirkuler_list == 'department_name' && filter.direction_detail_sirkuler_list == 'asc') ? true : false"
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
                          v-if="(filter.field_detail_sirkuler_list == 'department_name' && filter.direction_detail_sirkuler_list == 'desc') ? true : false"
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
                    <div class="flex items-center gap-4" @click="getDetailSirkuler('','company_name')">
                      <span
                        class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                        >Company</span
                      >
                      <div class="flex flex-col">
                        <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_detail_sirkuler_list == 'company_name' && filter.direction_detail_sirkuler_list == 'asc') ? true : false"
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
                          v-if="(filter.field_detail_sirkuler_list == 'company_name' && filter.direction_detail_sirkuler_list == 'desc') ? true : false"
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
                    <div class="flex items-center gap-4" @click="getDetailSirkuler('','updated_at')">
                      <span
                        class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                        >Date</span
                      >
                      <div class="flex flex-col">
                        <svg
                          class="inline-block"
                          xmlns="http://www.w3.org/2000/svg"
                          width="15px"
                          height="15px"
                          viewBox="0 0 15 15"
                          fill="none"
                          v-if="(filter.field_detail_sirkuler_list == 'updated_at' && filter.direction_detail_sirkuler_list == 'asc') ? true : false"
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
                          v-if="(filter.field_detail_sirkuler_list == 'updated_at' && filter.direction_detail_sirkuler_list == 'desc') ? true : false"
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
              </thead>
                <tbody>
                  <tr v-for="(data, index) in detail_sirkulers.data" :key="index">
                    <td data-label="Status">
                        <PillTag
                            :type="`success`"
                            :text="`Approved`"
                            :outline="false"
                            :small="true"
                            v-if="data.status == 2"
                        />
                        <PillTag
                            :type="`warning`"
                            :text="`Need Approval`"
                            :outline="false"
                            :small="true"
                            v-if="data.status == 1"
                        />
                        <PillTag
                            :type="`info`"
                            :text="`Memberikan Catatan`"
                            :outline="false"
                            :small="true"
                            v-if="data.status == 3"
                        />
                        <PillTag
                            :type="`danger`"
                            :text="`Cancel`"
                            :outline="false"
                            :small="true"
                            v-if="data.status == 5"
                        />
                    </td>
                    <td data-label="Name">
                      {{ data.name }}
                    </td>
                    <td data-label="Position">
                      {{data.position }}
                    </td>
                    <td data-label="Department">
                      {{data.department_name }}
                    </td>
                    <td data-label="Company">
                      {{data.company_name }}
                    </td>
                    <td data-label="Date">
                      {{ formatDateTime(data.updated_at) }}
                    </td>
                  </tr>
                </tbody>
              </table>
              <span>Total : {{ detail_sirkulers.total }}</span>
            </div>
            <div class="overflow-x-scroll w-96" v-if="detail_sirkulers.total > 5">
              <TailwindPagination
                :data="detail_sirkulers"
                :number="2"
                :limit="5"
                @pagination-change-page="getDetailSirkuler"
              />
            </div>
        </CardBoxModal>
        
        <div class="mb-3">
            <BaseButton
                type="submit"
                color="success"
                :label="`Refresh`"
                :route-name="`/sirkuler/tracking/${ticket_request_id}`"
            />
        </div>

        <div v-if="sirkuler_header.length == 0">
            <div class="m-5">
                <h1 class="text-center text-slate-950 dark:text-slate-50">Data Tidak Ditemukan.</h1>
                <h1 class="text-center text-slate-950 dark:text-slate-50">Hubungi Legal!</h1>
            </div>
        </div>

        <div class="-mx-3 md:flex mb-6" v-if="sirkuler_header.length > 0">
            <div class="md:w-1/3 px-3 mb-6 md:mb-0">
                <div class="-mx-3 md:flex mb-6" v-for="(value,index) in sirkuler_header" :key="index">
                  <div class="md:w-full px-3 mb-6 md:mb-0">
                        <BaseButton
                            type="submit"
                            color="info"
                            :label="value.code"
                            @click="getCatatanHistory(value)"
                        />
                  </div>
                </div>
            </div>
            <div class="md:w-full px-3 mb-6 md:mb-0" v-if="form.catatans.length > 0">
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3 mb-6 md:mb-0">
                        <FormField
                            label="Persetujuan"
                            :required="false"
                        >
                            <BaseButton
                                @click="showModalDetailSirkuler(filter.sirkuler_id)"
                                color="info"
                                :label="`Detail Persetujuan`"
                                small
                            />
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                        <FormField
                            label="Judul"
                            :required="false"
                        >
                        <FormControl
                              v-model="form.sirkuler_header.judul"
                              type="text"
                              placeholder="Input Judul..."
                              :readonly="true"
                          >
                          </FormControl>
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                        <FormField
                            label="NO Ticket"
                            :required="false"
                        >
                        <FormControl
                              v-model="form.sirkuler_header.no_ticket"
                              type="text"
                              placeholder="NO Ticket..."
                              :readonly="true"
                          >
                          </FormControl>
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                        <FormField
                            label="Opini Hukum"
                            :required="false"
                        >
                        <FormControl
                              v-model="form.sirkuler_header.opini_hukum"
                              type="text"
                              placeholder="Opini Hukum..."
                              :readonly="true"
                          >
                          </FormControl>
                        </FormField>
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3 mb-6 md:mb-0">
                        <FormField
                            label="Catatan History"
                            :required="false"
                        >
                        <div class="flex flex-col-reverse h-auto overflow-y-auto">
                            <div v-for="(data, index) in form.catatans" :key="index" class="">

                                <div class="dy-chat dy-chat-start">
                                    <div class="dy-chat-header">
                                        {{ data.name }}
                                        <time class="text-xs opacity-50">{{ formatDateTime(data.created_at) }}</time>
                                    </div>
                                    <div class="dy-chat-bubble" :class="{ 
                                        'dy-chat-bubble-primary': (data.color != 'warning') ? true : false, 
                                        'dy-chat-bubble-warning': (data.color == 'warning') ? true : false, 
                                        'dy-chat-bubble-error': (data.color == 'danger') ? true : false, 
                                        'dy-chat-bubble-accent': (data.color == 'accent') ? true : false,
                                        'dy-chat-bubble-success' : ((data.color != 'warning' && data.color != 'danger' && data.color != 'accent')) ? true : false 
                                        }">
                                        <span v-html="data.catatan"></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        </FormField>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
          <div v-if="!form.sirkuler_header.opini_hukum">
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
          </div>
        </template>
      </CardBox>
    </SectionMain>
  </LayoutGuest>
</template>
