<template>
    <div id="app-container">
        <DxDataGrid
            :data-source="dataSource"
			:remote-operations="true"
		    key-expr="EmployeeID">
			
			<DxPaging
				:page-size="20"
				:page-index="0" />
			
			<DxColumn
			  data-field="uuid"
			  caption="ID"
			>
			</DxColumn>
			
			<DxColumn
			  data-field="message"
			  caption="Message"
			>
			</DxColumn>
			
			<DxColumn
			  data-field="date"
			  caption="Add date"
			>
			</DxColumn>
			
			<DxEditing
                mode="popup"
                :allow-updating="true"
                :allow-adding="true"
                :allow-deleting="true"
            >
				<DxForm>
				  <DxItem
					:col-count="2"
					:col-span="2"
					item-type="group"
				  >
					<DxItem data-field="message"/>
				  </DxItem>
				</DxForm>
			</DxEditing>
			<DxSorting mode="single"/>
        </DxDataGrid>
    </div>
</template>
 
<script>
import config from '../config.js'
import 'devextreme/dist/css/dx.light.css';

import {
  DxDataGrid,
  DxPaging,
  DxColumn,
  DxEditing,
  DxForm,
  DxItem
} from 'devextreme-vue/data-grid';

import { createStore } from 'devextreme-aspnet-data-nojquery';

const dataSource = createStore({
  key: 'uuid',
  loadUrl: `${config.endpoint}?method=list`,
  insertUrl: `${config.endpoint}?action=insert`,
  updateUrl: `${config.endpoint}?action=update`,
  updateMethod: 'post',
  deleteUrl: `${config.endpoint}?action=delete`,
  deleteMethod: 'post',
  onBeforeSend: (method, ajaxOptions) => {
    ajaxOptions.xhrFields = { withCredentials: true };
  },
});
 
export default {
  components: {
    DxDataGrid,
	DxPaging,
    DxColumn,
	DxEditing,
	DxForm,
	DxItem,
  },
  data() {
    return {
      dataSource,
    };
  },
};
</script>