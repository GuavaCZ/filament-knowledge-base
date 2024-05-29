// resources/js/modals-component.js
function modalsComponent() {
  return {
    init: async function() {
      this.hashChanged();
      window.addEventListener("hashchange", () => this.hashChanged());
    },
    hashChanged: function() {
      const fragment = location.hash.substring(1);
      const prefix = "modal-";
      if (fragment.startsWith(prefix)) {
        const modal = fragment.substring(fragment.indexOf(prefix) + prefix.length);
        console.log("Open modal via wire: ", modal);
        this.$wire.showDocumentation(modal);
        history.replaceState(null, null, " ");
      }
    }
  };
}
export {
  modalsComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL21vZGFscy1jb21wb25lbnQuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIG1vZGFsc0NvbXBvbmVudCgpIHtcbiAgICByZXR1cm4ge1xuXG4gICAgICAgIGluaXQ6IGFzeW5jIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRoaXMuaGFzaENoYW5nZWQoKTtcbiAgICAgICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdoYXNoY2hhbmdlJywgKCk9PnRoaXMuaGFzaENoYW5nZWQoKSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgaGFzaENoYW5nZWQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgY29uc3QgZnJhZ21lbnQgPSBsb2NhdGlvbi5oYXNoLnN1YnN0cmluZygxKTtcbiAgICAgICAgICAgIGNvbnN0IHByZWZpeCA9ICdtb2RhbC0nO1xuXG4gICAgICAgICAgICBpZiAoZnJhZ21lbnQuc3RhcnRzV2l0aChwcmVmaXgpKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgbW9kYWwgPSBmcmFnbWVudC5zdWJzdHJpbmcoZnJhZ21lbnQuaW5kZXhPZihwcmVmaXgpICsgcHJlZml4Lmxlbmd0aCk7XG5cbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnT3BlbiBtb2RhbCB2aWEgd2lyZTogJywgbW9kYWwpO1xuICAgICAgICAgICAgICAgIHRoaXMuJHdpcmUuc2hvd0RvY3VtZW50YXRpb24obW9kYWwpO1xuICAgICAgICAgICAgICAgIC8vIHdpbmRvdy5kaXNwYXRjaEV2ZW50KG5ldyBDdXN0b21FdmVudCgnb3Blbi1tb2RhbCcsIHtkZXRhaWw6IHtpZDogbW9kYWx9fSkpO1xuICAgICAgICAgICAgICAgIGhpc3RvcnkucmVwbGFjZVN0YXRlKG51bGwsIG51bGwsICcgJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0sXG5cbiAgICB9XG5cbn07XG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQWUsU0FBUixrQkFBbUM7QUFDdEMsU0FBTztBQUFBLElBRUgsTUFBTSxpQkFBa0I7QUFDcEIsV0FBSyxZQUFZO0FBQ2pCLGFBQU8saUJBQWlCLGNBQWMsTUFBSSxLQUFLLFlBQVksQ0FBQztBQUFBLElBQ2hFO0FBQUEsSUFFQSxhQUFhLFdBQVc7QUFDcEIsWUFBTSxXQUFXLFNBQVMsS0FBSyxVQUFVLENBQUM7QUFDMUMsWUFBTSxTQUFTO0FBRWYsVUFBSSxTQUFTLFdBQVcsTUFBTSxHQUFHO0FBQzdCLGNBQU0sUUFBUSxTQUFTLFVBQVUsU0FBUyxRQUFRLE1BQU0sSUFBSSxPQUFPLE1BQU07QUFFekUsZ0JBQVEsSUFBSSx5QkFBeUIsS0FBSztBQUMxQyxhQUFLLE1BQU0sa0JBQWtCLEtBQUs7QUFFbEMsZ0JBQVEsYUFBYSxNQUFNLE1BQU0sR0FBRztBQUFBLE1BQ3hDO0FBQUEsSUFDSjtBQUFBLEVBRUo7QUFFSjsiLAogICJuYW1lcyI6IFtdCn0K
