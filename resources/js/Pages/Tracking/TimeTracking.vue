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
});

onMounted(() => {

});

</script>

<template>
  <LayoutGuest>
    <Head title="Time Tracking" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiFileDocumentEdit"
        title="Time Tracking"
        main
      >
        <span></span>
      </SectionTitleLineWithButton>
      <CardBox>

        <ul class="dy-timeline dy-timeline-vertical">
        <li>
            <div class="dy-timeline-start">03/05/2025 (total 02:00 jam)</div>
            <div class="dy-timeline-middle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            </div>
            <div class="dy-timeline-end dy-timeline-box">Persetujuan 1</div>
            <hr class="bg-success"/>
        </li>
        <li>
            <hr/>
            <div class="dy-timeline-start">03/05/2025 (total 01:00 jam)</div>
            <div class="dy-timeline-middle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            </div>
            <div class="dy-timeline-end dy-timeline-box">Persetujuan 2</div>
            <hr class="bg-success"/>
        </li>
        <li>
            <hr/>
            <div class="dy-timeline-start">03/05/2025 (total 01:00 jam)</div>
            <div class="dy-timeline-middle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            </div>
            <div class="dy-timeline-end dy-timeline-box">Persetujuan 3</div>
            <hr class="bg-success"/>
        </li>
        <li>
            <hr/>
            <div class="dy-timeline-start">03/05/2025 (total 01:00 jam)</div>
            <div class="dy-timeline-middle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            </div>
            <div class="dy-timeline-end dy-timeline-box">Persetujuan 4</div>
            <hr class="bg-success"/>
        </li>
        <li>
            <hr/>
            <div class="dy-timeline-start">03/05/2025 (total 01:30 jam)</div>
            <div class="dy-timeline-middle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
            </div>
            <div class="dy-timeline-end dy-timeline-box">Persetujuan 5</div>
        </li>
        </ul>

        <template #footer>
          <div>
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
