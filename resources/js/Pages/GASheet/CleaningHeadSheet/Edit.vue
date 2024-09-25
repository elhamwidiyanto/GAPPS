 <style src="vue-multiselect/dist/vue-multiselect.css"></style>

<script setup>
import { onMounted, inject, reactive, ref, computed } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import { mdiAccountArrowUp, mdiArrowLeftBoldOutline, mdiFileEye } from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import Multiselect from "vue-multiselect";
import moment from "moment";
import useNumberFormat from "../../../Helpers/numberFormat";
import FormControl from '@/Components/FormControl.vue';
import CardBoxModal from '@/Components/CardBoxModal.vue';
import BaseDivider from "@/Components/BaseDivider.vue";
import axios from "axios";
import { forEach } from "lodash";
 
const props = defineProps({
    app_url: {
        type: String,
        default: "",
    },
    cleaning_header: {
        type: Object,
        default: () => {},
    },
    master_simbol_kondisis: {
        type: Array,
        default: () => [],
    },
    simbol_kondisi_id_arr: {
        type: Array,
        default: () => [],
    },
    master_pics: {
        type: Array,
        default: () => [],
    },
    query_get_pic_choosed: {
        type: Object,
        default: () => {},
    },
    master_lokasis: {
        type: Array,
        default: () => [],
    },
    master_lokasi_choosed: {
        type: Object,
        default: () => {},
    },
    master_gedungs: {
        type: Array,
        default: () => [],
    },
    master_gedung_choosed: {
        type: Object,
        default: () => {},
    },
    master_ruangans: {
        type: Array,
        default: () => [],
    },
    master_ruangan_choosed: {
        type: Object,
        default: () => {},
    },
    cleaning_tx: {
        type: Array,
        default: () => [],
    },
    master_alats: {
        type: Array,
        default: () => [],
    },
    master_alat_choosed: {
        type: Object,
        default: () => [],
    },
});

onMounted(() => {
    form.selected_value = props.simbol_kondisi_id_arr;

    form.cleaning_tx = props.cleaning_tx;

    props.cleaning_tx.forEach((value, index) => {
        form.is_checked.push(value.is_checked == 1 ? true : false);
        form.description.push(value.description);
    });

    get_json_index();
});

const { numberFormat } = useNumberFormat();

const formatDateTime = (date) => {
    const day = moment(date).format("DD");
    const month = moment(date).format("MMM");
    const year = moment(date).format("YYYY");
    const hour = moment(date).format("HH");
    const minute = moment(date).format("mm");
    return `${day}/${month}/${year} ${hour}:${minute}`;
};

const swal = inject("$swal");
const disabled = ref(false);
const form = reactive({
    items: [],
    simbol: null,
    value_location: props.master_lokasi_choosed,
    value_gedung: props.master_gedung_choosed,
    value_ruangan: props.master_ruangan_choosed,
    value_alat: props.master_alat_choosed,
    master_alats: [],
    master_simbol_kondisis: [],
    description: [],
    selected_value: [],
    is_checked: [],
    id: [],
    selected_pic: props.query_get_pic_choosed,
    cleaning_tx: [],

});
const filter = reactive({
  is_show_signature: false,  
  is_show_modal_reject: false,  
});

const delay = async (value) => {
    // disabled.value = true;

    submit(value);
};

const get_json_index = async (page = 1, field = "") => {
    // your logic for fetching data
};

const changeLocation = () => {
    form.value_gedung = null;
    form.value_ruangan = null;
};

const changeGedung = () => {
    form.value_ruangan = null;
};

const changeRuangan = async (value) => {
    try {
        const response = await axios.post(
            `/ga_sheet/cleaning/get_alat_simbol_kondisi`,
            {
                value_location: form.value_location,
                value_gedung: form.value_gedung,
                value_ruangan: value,
            }
        );
        if (!value) {
            form.master_alats = [];
        } else {
            form.master_alats = response.data.data_alat;
            form.master_simbol_kondisis = response.data.data_simbol_kondisi;
            form.description = response.data.data_alat.map(() => "");
        }
    } catch (error) {
        console.error(error);
    }
};

const submit = () => {
    //send data to server
    router.post(
        `/ga_sheet/cleaning/${props.cleaning_header.id}`,
        {
            _method: "PUT",
            master_alats: form.master_alats,
            simbol_name: form.selected_value,
            is_checked: form.is_checked,
            description: form.description,
            pic: form.selected_pic,
            location: form.value_location,
            gedung: form.value_gedung,
            ruangan: form.value_ruangan,
            cleaning_tx: form.cleaning_tx,
        },
        {
            onSuccess: () => {
                //show success alert
                swal({
                    title: "Berhasil!",
                    text: "cleaning berhasil ditambahkan!",
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

function openModalReject(value) {
  filter.is_show_modal_reject = true;
  form.id_data = value,
  console.log(filter.is_show_modal_reject);
  console.log(value)
}

const delayApprove = async (data) => {
        form.value = 2
        swal({
            title: "Informasi",
            text: "Data disetujui",
            icon: "success",
            showConfirmButton: false,
            timer: 2000,
        });
        await submit_status(data);
    // disabled.value = true; 
};
const delayReject = async (data) => {

    form.value = 3  
    // var reason_reject =swal({
    //                           type: 'button',
    //                           title: 'Alasan Ditolak?',
    //                           input: 'text',
    //                           inputPlaceholder: 'GUA, JUGA SAYANG SAMA LU TAPI SEBAGAI TEMEN',
    //                           showCloseButton: true,
                            
    //                       });
    await submit_status(data);
};
const submit_status = async (data) =>{

    if(form.value == 3 && (form.reason_reject == null || form.reason_reject == '')){

      swal({
            title: "Peringatan!",
            text: "Harap masukkan alasan penolakkan!",
            icon: "warning",
            showConfirmButton: false, 
            timer: 2000,
        });

        return;

    }
    console.log(data); 

    axios.post(`/ga_sheet/cleaning_head/status`, {
        id: data.id,
        status: form.value,
        reason_reject: form.reason_reject,
        is_checked: form.is_checked,
        pic: form.selected_pic,

    })
    .then(response => {
        console.log(response.data);
        router.visit('/ga_sheet/cleaning_head');
    })
    .catch(error => {
        console.error(error);
        // Handle error, maybe show an error message
    });
};

const filteredGedung = computed(() => {
    if (form.value_location) {
        return props.master_gedungs.filter(
            (gedung) => gedung.id_lokasi === form.value_location.id
        );
    }
    return [];
});

const filteredRuangan = computed(() => {
    if (form.value_gedung) {
        return props.master_ruangans.filter(
            (ruangan) => ruangan.id_gedung === form.value_gedung.id
        );
    }
    return [];
});
// const filteredAlat = computed(() => {
//   if (form.value_ruangan) {
//     return props.master_alats.filter(alat => alat.ruangan_id === form.value_ruangan.id)
//   }
//   return []
// })

const selectValue = () => {
    for (let i = 0; i < form.master_alats.length; i++) {
        if (!form.selected_value[i]) {
            form.selected_value[i] = { id: null, name: null }; // Ensure a consistent structure
        }
    }
};

</script>

<template>
    <LayoutAuthenticated>
        <Head title="Cleaning Sheet" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiFileEye"
                title="Detail Cleaning"
                main
            >
                <BaseButton
                    :route-name="route('cleaning_head.index')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Kembali"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>

                            <!-- signature modal -->
                            <CardBoxModal
                v-model="filter.is_show_modal_reject"
                large-title="Alasan penolakkan!"
                button="danger"
                has-cancel
                buttonLabel="Submit"
                :buttonCancelSmall="true"
                :buttonConfirmSmall="true"
                :colorButtonCancel="`warning`"
                :colorButtonConfirm="`danger`"
                :hasConfirm="true"
                :hasCancel="true"
                @confirm="delayReject(form.id_data)"
            >

            <div>
                <FormControl
                    v-model="form.reason_reject"
                    type="textarea"
                    placeholder="Input alasan penolakkan..."
                >
                </FormControl>

                </div>

            </CardBoxModal>

            <div class="flex flex-wrap justify-center mb-6">
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField label="Lokasi" :required="true">
                        <Multiselect
                            v-model="form.value_location"
                            :options="props.master_lokasis"
                            placeholder="Pilih Lokasi"
                            label="name"
                            @update:modelValue="changeLocation"
                            track-by="id"
                            :disabled="true"
                        />
                    </FormField>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField label="Gedung" :required="true">
                        <Multiselect
                            v-model="form.value_gedung"
                            :options="filteredGedung"
                            placeholder="Pilih Gedung"
                            @update:modelValue="changeGedung"
                            label="name"
                            track-by="id"
                            :disabled="true"
                        />
                    </FormField>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField label="Ruangan" :required="true">
                        <Multiselect
                            v-model="form.value_ruangan"
                            :options="filteredRuangan"
                            placeholder="Pilih Ruangan"
                            @update:modelValue="changeRuangan"
                            label="name"
                            track-by="id"
                            :disabled="true"
                        />
                    </FormField>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <FormField label="PIC" :required="true">
                        <Multiselect
                        
                            v-model="form.selected_pic"
                            :options="props.master_pics"
                            placeholder="Pilih PIC"
                            label="pic_name"
                            track-by="id"
                        />
                    </FormField>
                </div>
            </div>
            <CardBox class="mb-6" has-table v-if="form.cleaning_tx.length > 0">
                <div class="overflow-x-auto">
                    <table
                        class="table table-responsive px-auto text-slate-950 dark:text-slate-50"
                    >
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Komponen</th>
                                <th class="text-center">Checklist</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(data, id) in form.cleaning_tx"
                                :key="id"
                            >
                                <td class="text-center">{{ id+1 }}</td>
                                <td class="text-center">{{ data.alat_name }}</td>
                                <td style="text-align: center">
                                    <input
                                        name="data.form.checkbox"
                                        type="checkbox"
                                        v-model="form.is_checked[id]"
                                        :disabled="true"
                                    />
                                </td>
                                <td>
                                    <Multiselect
                                        v-model="form.selected_value[id]"
                                        :options="props.master_simbol_kondisis"
                                        placeholder="Pilih Salah Satu"
                                        label="name"
                                        track-by="id"
                                        open-direction="bottom"
                                        :allow-empty="false"
                                        :disabled="true"
                                    />
                                </td>
                                <td style="text-align: center">
                                    <textarea
                                        id="description"
                                        v-model="form.description[id]"
                                        class="text-black textarea textarea-bordered rounded-md"
                                        placeholder="Remark"
                                        :disabled="true"
                                    ></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <template #footer>
                    <BaseButtons class="flex justify-center lg:justify-end w-full space-x-2" no-wrap>
                        <div v-if="props.cleaning_header.status == 1">
                            <BaseButton
                            color="success" 
                            label="Setujui"
                            v-model="form.value"
                            small
                            class="w-20" 
                            @click="delayApprove(props.cleaning_header)"
                        />
                        <BaseButton
                            color="danger"
                            label="Tolak"
                            v-model="form.value"
                            small
                            class="w-20" 
                            @click="openModalReject(props.cleaning_header)" 
                        />
                        </div>
                        <div v-else-if="props.cleaning_header.status == 2 || props.cleaning_header.status == 4">
                            <BaseButton
                            color="success"
                            label="Setujui"
                            v-model="form.value"
                            small
                            class="w-20" 
                            @click="delayApprove(props.cleaning_header)"
                            :disabled="true"
                        />
                        <BaseButton
                            color="danger"
                            label="Tolak"
                            v-model="form.value"
                            small
                            class="w-20" 
                            @click="openModalReject(props.cleaning_header)" 
                            :disabled="true"
                        />
                        </div>
                        <div v-else-if="props.cleaning_header.status == 3">
                            <BaseButton
                            color="success"
                            label="Setujui"
                            v-model="form.value"
                            small
                            class="w-20" 
                            @click="delayApprove(props.cleaning_header)"
                        />
                        <BaseButton
                            color="danger"
                            label="Tolak"
                            v-model="form.value"
                            small
                            class="w-20" 
                            @click="openModalReject(props.cleaning_header)" 
                            :disabled="true"
                        />
                        </div>
                    </BaseButtons>
                </template>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
    </template>