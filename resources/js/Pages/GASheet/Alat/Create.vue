<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed, nextTick } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import {
    mdiAccountKey,
    mdiArrowLeftBoldOutline,
    mdiFileDocumentEdit,
    mdiAccountSwitch,
    mdiTrashCanOutline,
    mdiClipboardListOutline,
    mdiStateMachine,
    mdiAccountArrowUp,
    mdiFileDocument,
    mdiFileDocumentEditOutline,
    mdiFileDocumentPlus
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
// import FormFilePicker from "@/components/FormFilePicker.vue";
// import BaseDivider from "@/components/BaseDivider.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "vue-multiselect";
import moment from "moment";
import useNumberFormat from "../../../Helpers/numberFormat";

import axios from "axios";

const props = defineProps({
    app_url: {
        type: String,
        default: () => "",
    },
    master_types: {
        type: Array,
        default: () => [],
    },
});

const { numberFormat } = useNumberFormat();

onMounted(() => {
    console.log(props.master_types)
});

const format = (date) => {
    const day = date.getDate();
    const month = moment(date).format("MMM");
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
};

const swal = inject("$swal");
const disabled = ref(false);
const disabled_form = ref(false);

const form = reactive({
    name: "",
    is_active: false,
    value: [],
    alats: [],
    default_kondisi: [],
    simbols: [],
    master_lokasis: [],
    master_gedungs: [],
    master_ruangans: [],
    selected_gedung: [],
    selected_lokasi: [],
    selected_ruangan: [],
});


const delay = async (value) => {
    if (form.name === "" || form.name == null) {
        swal({
            title: "Peringatan!",
            text: "Nama wajib diisi!",
            icon: "warning",
            showConfirmButton: false,
            timer: 2000,
        });

        return;
    }

    disabled.value = true;

    submit(value);
};

const submit = (value) => {
    router.post(
        `/ga_sheet/alat`,
        {
            name: form.name,
            is_active: form.is_active,
            type_id: form.value.id,
            default_simbol: form.default_kondisi.id,
            lokasi_id : form.selected_lokasi.id,
            gedung_id : form.selected_gedung.id,
            ruangan_id : form.selected_ruangan.id
        },
        {
            onSuccess: () => {
                swal({
                    title: "Berhasil!",
                    text: "Alat berhasil ditambahkan!",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 2000,
                });
            },
            onError: () => {
                disabled.value = false;
            },
        }
    );
};
const checkSheet = (value) => {
    if(value.id === 1){
        getLokasi(value);
        console.log("kekekek")
    }else{
        form.master_lokasis = [],
        form.master_gedungs = [],
        form.master_ruangans =[]
    }
}

const getLokasi= async(value) => {
    console.log("KONTOL")
    try{
        const response = await axios.post(`/ga_sheet/simbol_kondisi/get_lokasi`, {

        })
        form.master_lokasis = response.data.lokasis
        console.log(form.master_lokasis)
        form.master_gedungs = [],
        form.master_ruangans =[]
    }catch (error) {
            console.error(error);
        }
}

const getGedung= async(value) => {
    try{
        const response = await axios.post(`/ga_sheet/simbol_kondisi/get_gedung`, {
            lokasi_id : value.id
        })
        form.master_gedungs = response.data.gedungs
        form.master_ruangans =[]
    }catch (error) {
            console.error(error);
        }
}

const getRuangan = async(value) => {
    try{
        const response = await axios.post(`/ga_sheet/simbol_kondisi/get_ruangan`, {
            gedung_id : value.id
        })
        form.master_ruangans = response.data.ruangans
        form.alats = []
    }catch (error) {
            console.error(error);
        }
}

// const get_alat_cleaning = async(value) => {
//     form.selected_alat = null
//     if(value){
//         try {
//         const response = await axios.post(
//             `/ga_sheet/simbol_kondisi/get_alat_cleaning`,
//             {
//                 lokasi_id : form.selected_lokasi,
//                 gedung_id : form.selected_gedung,
//                 ruangan_id : form.selected_ruangan
//             }
            
//         );
//         form.alats = response.data.alats
//         } catch (error) {
//             console.error(error);
//         }
//     }else{
//         form.selected_alat = null
//         form.alats = []

//     }
// }
const getDefaultSimbol = async() => {
        try {
        const response = await axios.post(
            `/ga_sheet/alat/get_default_simbol`,
            {
                lokasi_id : form.selected_lokasi.id,
                gedung_id : form.selected_gedung.id,
                ruangan_id : form.selected_ruangan.id
            }
            
        );
        form.simbols = response.data.simbol_kondisis
        } catch (error) {
            console.error(error);
        }
}


const get_alat = async(value) => {
    form.selected_alat = null
    if(value){
        try {
        const response = await axios.post(
            `/ga_sheet/simbol_kondisi/get_alat`,
            {
                sheet: value,
            }
            
        );
        form.alats = response.data.alats
        } catch (error) {
            console.error(error);
        }
    }else{
        form.selected_alat = null
        form.alats = []

    }
}



</script>

<template>
    <LayoutAuthenticated>
        <Head title="Alat" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiFileDocumentPlus"
                title="Tambah Alat"
                main
            >
                <BaseButton
                    :route-name="route('alat.index')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Kembali"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>
            <CardBox>
                <div class="-mx-3 md:flex ml-0 mb-2">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Nama" :required="true">
                            <FormControl
                                v-model="form.name"
                                type="text"
                                placeholder="Input Nama..."
                            >
                            </FormControl>
                        </FormField>
                    </div>
                </div>
                    <div class="md:w-1/2 px-3 md:mb-2">
                            <FormField label="Pilih Sheet" :required="true">
                                <multiselect
                                    v-model="form.value"
                                    :options="props.master_types"
                                    label="name"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    @update:modelValue="checkSheet"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                            <FormField label="Pilih Lokasi" :required="true" class="-mt-4" v-if="form.value.id === 1">
                                <multiselect
                                    v-model="form.selected_lokasi"
                                    :options="form.master_lokasis"
                                    label="name"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    @update:modelValue="getGedung"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                            <FormField label="Pilih Gedung" :required="true" class="-mt-4" v-if="form.value.id === 1">
                                <multiselect
                                    v-model="form.selected_gedung"
                                    :options="form.master_gedungs"
                                    :searchable="true"
                                    label="name"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    @update:modelValue="getRuangan"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                            <FormField label="Pilih Ruangan" :required="true" class="-mt-4" v-if="form.value.id === 1">
                                <multiselect
                                    v-model="form.selected_ruangan"
                                    :options="form.master_ruangans"
                                    :searchable="true"
                                    label="name"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                    @update:modelValue="getDefaultSimbol"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                            <FormField label="Pilih Kondisi Default" :required="true" class="-mt-2" v-if="form.value.id === 1">
                                <multiselect
                                    v-model="form.default_kondisi"
                                    :options="form.simbols"
                                    label="name"
                                    :searchable="true"
                                    :close-on-select="true"
                                    :show-labels="true"
                                    open-direction="bottom"
                                    placeholder="Pick a value"
                                ></multiselect>
                                <pre class="language-json"></pre>
                            </FormField>
                    </div>
                <div class="mt-2">
                    <div class="md:w-1/4 px-3 mb-6 md:mb-0">
                        <FormField label="Status" :required="true">
                            <FormCheckRadioGroup
                                v-model="form.is_active"
                                type="switch"
                                name="notifications-switch"
                                :options="{ outline: '' }"
                                class="mb-2"
                            />
                        </FormField>
                    </div>
                </div>
                <template #footer>
                    <BaseButtons class="flex justify-end">
                        <BaseButton
                            type="submit"
                            color="success"
                            label="Simpan"
                            :class="{ 'opacity-25': disabled }"
                            :disabled="disabled"
                            @click="delay(1)"
                        />
                    </BaseButtons>
                </template>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>