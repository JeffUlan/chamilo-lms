import isEmpty from 'lodash/isEmpty';
import { formatDateTime } from '../utils/dates';
import NotificationMixin from './NotificationMixin';

export default {
  mixins: [NotificationMixin],

  data() {
    return {
      options: {
        sortBy: [],
        descending: false,
        page: 1,
        itemsPerPage: 15
      },
      filters: {}
    };
  },

  watch: {
    deletedItem(item) {
      this.showMessage(`${item['@id']} deleted.`);
    },

    error(message) {
      message && this.showError(message);
    },

    items() {
      this.options.totalItems = this.totalItems;
    }
  },

  methods: {
    onUpdateOptions(props) {
      const { page, itemsPerPage, sortBy, sortDesc, descending, totalItems } = props;
      let params = {
        ...this.filters
      };
      if (itemsPerPage > 0) {
        params = { ...params, itemsPerPage, page };
      }

      let sortDescVuetify = false;
      let vueDescending = descending;
      if (sortBy.length === 1 && sortDesc.length === 1) {
        if (sortDesc[0]) {
          sortDescVuetify = true;
        }
        vueDescending = sortDescVuetify;
      }

      if (!isEmpty(sortBy)) {
        params[`order[${sortBy}]`] = vueDescending ? 'desc' : 'asc';
      }

      this.resetList = true;

      this.getPage(params).then(() => {
        this.options.sortBy = sortBy;
        this.options.descending = descending;
        this.options.itemsPerPage = itemsPerPage;
        this.options.totalItems = totalItems;
      });
    },

    onSendFilter() {
      this.resetList = true;
      this.onUpdateOptions(this.options);
    },

    resetFilter() {
      this.filters = {};
    },

    addHandler() {
      this.$router.push({ name: `${this.$options.servicePrefix}Create` });
    },

    addDocumentHandler() {
      this.$router.push({ name: `${this.$options.servicePrefix}CreateFile` });
    },

    showHandler(item) {
      this.$router.push({
        name: `${this.$options.servicePrefix}Show`,
        params: { id: item['@id'] }
      });
    },

    editHandler(item) {
      this.$router.push({
        name: `${this.$options.servicePrefix}Update`,
        params: { id: item['@id'] }
      });
    },

    deleteHandler(item) {
      this.deleteItem(item).then(() => this.onUpdateOptions(this.options));
    },
    formatDateTime
  }
};
