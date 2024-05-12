// resources/js/anchors-component.js
function anchorsComponent() {
  return {
    init: async function() {
      let anchors = document.querySelectorAll(".gu-kb-anchor");
      let settings = {
        root: null,
        rootMargin: "-15% 0px -65% 0px",
        threshold: 0.1
      };
      let observer = new IntersectionObserver(this.callback, settings);
      anchors.forEach((anchor) => observer.observe(anchor));
    },
    callback: function(entries, observer) {
      let classes = [
        "transition",
        "duration-300",
        "ease-out",
        "text-primary-600",
        "dark:text-primary-400",
        "translate-x-1"
      ];
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          let section = "#" + entry.target.id;
          document.querySelectorAll(".fi-sidebar-item-button .fi-sidebar-item-label").forEach((el2) => el2.classList.remove(...classes));
          let el = document.querySelector(".fi-sidebar-item-button[href='" + section + "'] .fi-sidebar-item-label");
          el.classList.add(...classes);
        }
      });
    }
  };
}
export {
  anchorsComponent as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsiLi4vLi4vcmVzb3VyY2VzL2pzL2FuY2hvcnMtY29tcG9uZW50LmpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyJleHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBhbmNob3JzQ29tcG9uZW50KCkge1xuICAgIHJldHVybiB7XG5cbiAgICAgICAgaW5pdDogYXN5bmMgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbGV0IGFuY2hvcnMgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcuZ3Uta2ItYW5jaG9yJyk7XG5cbiAgICAgICAgICAgIGxldCBzZXR0aW5ncyA9IHtcbiAgICAgICAgICAgICAgICByb290OiBudWxsLFxuICAgICAgICAgICAgICAgIHJvb3RNYXJnaW46ICctMTUlIDBweCAtNjUlIDBweCcsXG4gICAgICAgICAgICAgICAgdGhyZXNob2xkOiAwLjEsXG4gICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICBsZXQgb2JzZXJ2ZXIgPSBuZXcgSW50ZXJzZWN0aW9uT2JzZXJ2ZXIodGhpcy5jYWxsYmFjaywgc2V0dGluZ3MpO1xuXG4gICAgICAgICAgICBhbmNob3JzLmZvckVhY2goYW5jaG9yID0+IG9ic2VydmVyLm9ic2VydmUoYW5jaG9yKSk7XG4gICAgICAgIH0sXG5cbiAgICAgICAgY2FsbGJhY2s6IGZ1bmN0aW9uIChlbnRyaWVzLCBvYnNlcnZlcikge1xuICAgICAgICAgICAgbGV0IGNsYXNzZXMgPSBbXG4gICAgICAgICAgICAgICAgJ3RyYW5zaXRpb24nLCAnZHVyYXRpb24tMzAwJywgJ2Vhc2Utb3V0JywgJ3RleHQtcHJpbWFyeS02MDAnLCAnZGFyazp0ZXh0LXByaW1hcnktNDAwJywgJ3RyYW5zbGF0ZS14LTEnXG4gICAgICAgICAgICBdO1xuXG4gICAgICAgICAgICBlbnRyaWVzLmZvckVhY2goZW50cnkgPT4ge1xuICAgICAgICAgICAgICAgIGlmIChlbnRyeS5pc0ludGVyc2VjdGluZykge1xuICAgICAgICAgICAgICAgICAgICBsZXQgc2VjdGlvbiA9ICcjJyArIGVudHJ5LnRhcmdldC5pZDtcbiAgICAgICAgICAgICAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLmZpLXNpZGViYXItaXRlbS1idXR0b24gLmZpLXNpZGViYXItaXRlbS1sYWJlbCcpXG4gICAgICAgICAgICAgICAgICAgICAgICAuZm9yRWFjaCgoZWwpID0+IGVsLmNsYXNzTGlzdC5yZW1vdmUoLi4uY2xhc3NlcykpO1xuICAgICAgICAgICAgICAgICAgICBsZXQgZWwgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcuZmktc2lkZWJhci1pdGVtLWJ1dHRvbltocmVmPVxcJycgKyBzZWN0aW9uICsgJ1xcJ10gLmZpLXNpZGViYXItaXRlbS1sYWJlbCcpO1xuICAgICAgICAgICAgICAgICAgICBlbC5jbGFzc0xpc3QuYWRkKC4uLmNsYXNzZXMpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuXG59O1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFlLFNBQVIsbUJBQW9DO0FBQ3ZDLFNBQU87QUFBQSxJQUVILE1BQU0saUJBQWtCO0FBQ3BCLFVBQUksVUFBVSxTQUFTLGlCQUFpQixlQUFlO0FBRXZELFVBQUksV0FBVztBQUFBLFFBQ1gsTUFBTTtBQUFBLFFBQ04sWUFBWTtBQUFBLFFBQ1osV0FBVztBQUFBLE1BQ2Y7QUFFQSxVQUFJLFdBQVcsSUFBSSxxQkFBcUIsS0FBSyxVQUFVLFFBQVE7QUFFL0QsY0FBUSxRQUFRLFlBQVUsU0FBUyxRQUFRLE1BQU0sQ0FBQztBQUFBLElBQ3REO0FBQUEsSUFFQSxVQUFVLFNBQVUsU0FBUyxVQUFVO0FBQ25DLFVBQUksVUFBVTtBQUFBLFFBQ1Y7QUFBQSxRQUFjO0FBQUEsUUFBZ0I7QUFBQSxRQUFZO0FBQUEsUUFBb0I7QUFBQSxRQUF5QjtBQUFBLE1BQzNGO0FBRUEsY0FBUSxRQUFRLFdBQVM7QUFDckIsWUFBSSxNQUFNLGdCQUFnQjtBQUN0QixjQUFJLFVBQVUsTUFBTSxNQUFNLE9BQU87QUFDakMsbUJBQVMsaUJBQWlCLGdEQUFnRCxFQUNyRSxRQUFRLENBQUNBLFFBQU9BLElBQUcsVUFBVSxPQUFPLEdBQUcsT0FBTyxDQUFDO0FBQ3BELGNBQUksS0FBSyxTQUFTLGNBQWMsbUNBQW9DLFVBQVUsMkJBQTRCO0FBQzFHLGFBQUcsVUFBVSxJQUFJLEdBQUcsT0FBTztBQUFBLFFBQy9CO0FBQUEsTUFDSixDQUFDO0FBQUEsSUFDTDtBQUFBLEVBQ0o7QUFFSjsiLAogICJuYW1lcyI6IFsiZWwiXQp9Cg==
