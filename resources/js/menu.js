import {
  mdiAccountCircle,
  mdiMonitor,
  mdiGithub,
  mdiAccountKey,
  mdiAccountEye,
  mdiAccountGroup,
  mdiPalette,
  mdiFileDocumentEdit,
  mdiSitemapOutline,
  mdiListBoxOutline,
  mdiAccountSwitch,
  mdiSecurity,
  mdiClipboardListOutline,
  mdiCheckDecagramOutline,
  mdiCalendarMonthOutline,
  mdiCalendarMonth,
  mdiStateMachine,
  mdiWarehouse,
  mdiAccountArrowUp,
  mdiTruckCargoContainer,
  mdiCash,
  mdiBottleSodaClassicOutline,
  mdiSquareEditOutline,
  mdiMagnifyScan,
  mdiWalletPlus,
  mdiAccountArrowLeft,
  mdiQrcodeScan,
  mdiHomeCircle,
  mdiViewColumn,
  mdiVacuumOutline,
  mdiPaperCutVertical,
  mdiBookOpenPageVariant,
  mdiToilet,
  mdiCheckbook,
  mdiSpray,
  mdiSprayBottle,
  mdiFileDocumentCheck,
  mdiDesktopClassic,
  mdiFileDocument,
  mdiWrench,
  mdiBroom,
  mdiAccountPlus,
  mdiAccount
} from '@mdi/js'

export default [
  // {
  //   link: '/dashboard',
  //   icon: mdiMonitor,
  //   name: 'Dashboard',
  //   route: 'dashboard'
  // },
  {
    link: '/sirkuler/home',
    icon: mdiHomeCircle,
    name: 'Beranda',
  },
  // {
  //   name: "Sirkuler",
  //   icon: mdiFileDocumentEdit,
  //   permission: 'sirkuler_create list|sirkuler_create_head_legal list',
  //   children: [
  //     {
  //       name: "Admin",
  //       icon: mdiFileDocumentEdit,
  //       permission: 'sirkuler_create list',
  //       link: '/sirkuler/createlist',
  //     },
  //     {
  //       name: "Atasan",
  //       icon: mdiFileDocumentEdit,
  //       permission: 'sirkuler_create_head_legal list',
  //       link: '/sirkuler/head_legal_sirkuler',
  //     },
  //   ]
  // },
  // {
  //   name: "Persetujuan",
  //   icon: mdiFileDocumentEdit,
  //   // permission: 'approval_sirkuler list',
  //   link: '/sirkuler/approval_sirkuler',
  // },
  {
    name: "Sheet",
    icon: mdiFileDocument,
    permission: 'cleaning list',
    children: [
      { 
        link: '/ga_sheet/cleaning',
        icon: mdiBroom,
        name: 'Cleaning Sheet',
        permission: 'cleaning list',
      },
      { 
        link: '/ga_sheet/maintenance',
        icon: mdiWrench,
        name: 'Maintenance Sheet',
        permission: 'master_maintenance list',
      },
      { 
        link: '/ga_sheet/it',
        icon: mdiDesktopClassic,
        name: 'IT Sheet',
        permission: 'it list',
      },
    ]    
  },
  { 
    name: "Sheet (Head)",
    icon: mdiFileDocumentCheck,
    permission: 'cleaning_head list',
    children: [
      { 
        link: '/ga_sheet/cleaning_head',
        icon: mdiBroom,
        name: 'Cleaning Head Sheet',
        permission: 'cleaning_head list',
      },
      { 
        link: '/ga_sheet/maintenance_head',
        icon: mdiWrench,
        name: 'Maintenance Head Sheet',
        permission: 'maintenance_head list',
      },
      { 
        link: '/ga_sheet/it_head',
        icon: mdiDesktopClassic,
        name: 'IT Heaad Sheet',
        permission: 'it_head list',
      }, 
    ]   
  },
  { 
    name: 'Report',
    icon: mdiCheckbook,
    link: '/ga_sheet/report',
  },
  {
    name: "Master",
    icon: mdiAccount,
    permission: 'master_simbol_kondisi list',
    children: [
      {
        link: '/ga_sheet/simbol_kondisi',
        icon: mdiAccountPlus,
        name: 'Simbol Kondisi',
        permission: 'master_simbol_kondisi list',
      }, 
      {
        link: '/ga_sheet/pic',
        icon: mdiAccountPlus,
        name: 'PIC',
        permission: 'master_pic list',
      },
      {
        link: '/ga_sheet/category_preventive_breakdown',
        icon: mdiAccountPlus,
        name: 'Category Preventive Breakdown',
        permission: 'master_category_preventive_breakdown list',
      },
      {
        link: '/ga_sheet/number_of_work',
        icon: mdiAccountPlus,
        name: 'Number Of Work',
        permission: 'master_number_of_work list',
      },
      {
        link: '/ga_sheet/alat',
        icon: mdiAccountPlus,
        name: 'Alat',
        permission: 'master_alat list',
      },
      {
        link: '/ga_sheet/komponen',
        icon: mdiAccountPlus,
        name: 'Komponen',
        permission: 'master_alat list',
      },
      {
        link: '/ga_sheet/lokasi',
        icon: mdiAccountPlus,
        name: 'Lokasi',
        permission: 'master_lokasi list',
      },
      {
        link: '/ga_sheet/gedung',
        icon: mdiAccountPlus,
        name: 'Gedung',
        permission: 'master_gedung list',
      },
      {
        link: '/ga_sheet/ruangan',
        icon: mdiAccountPlus,
        name: 'Ruangan',
        permission: 'master_ruangan list',
      },
      {
        link: '/ga_sheet/sheet_roles',
        icon: mdiAccountPlus,
        name: 'Roles',
        permission: 'sheet_roles list',
      },
    ]  
  },
  {
    name: "Administrator",
    icon: mdiSecurity,
    permission: 'permission list|role list|user list',
    children: [
      {
        link: '/admin/permission',
        icon: mdiAccountKey,
        name: 'Permissions',
        permission: 'permission list',
      },
      {
        link: '/admin/role',
        icon: mdiAccountEye,
        name: 'Roles',
        permission: 'role list'
      },
      {
        link: '/admin/user',
        icon: mdiAccountGroup,
        name: 'Users',
        permission: 'user list'
      },
    ],
  },
  // {
  //   link: 'https://github.com/balajidharma/laravel-vue-admin-panel',
  //   name: 'GitHub',
  //   icon: mdiGithub,
  //   target: '_blank'
  // }
]