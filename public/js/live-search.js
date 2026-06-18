document.addEventListener('DOMContentLoaded', function() {
    let debounceTimer;
    
    document.body.addEventListener('input', function(e) {
        if (e.target.classList.contains('live-search-input') || e.target.classList.contains('live-filter-input')) {
            clearTimeout(debounceTimer);
            
            debounceTimer = setTimeout(() => {
                const form = e.target.closest('form');
                if (!form) return;
                
                // Show loading state if available
                const loadingOverlay = document.querySelector('.table-loading-overlay');
                if (loadingOverlay) loadingOverlay.style.display = 'flex';
                
                const url = new URL(form.action || window.location.href);
                const formData = new FormData(form);
                
                // Convert formData to URL parameters
                for (let [key, value] of formData.entries()) {
                    if (value) {
                        url.searchParams.set(key, value);
                    } else {
                        url.searchParams.delete(key);
                    }
                }
                
                // Fetch new HTML
                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Replace table container
                    const newTable = doc.querySelector('#table-container');
                    const currentTable = document.querySelector('#table-container');
                    
                    if (newTable && currentTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                        
                        // Re-evaluate scripts or Alpine if needed
                        if (window.Alpine) {
                            // Discover components inside the new table to initialize Alpine directives
                            window.Alpine.initTree(currentTable);
                        }
                    }
                    
                    // Also replace pagination container if it's outside
                    const newPagination = doc.querySelector('#pagination-container');
                    const currentPagination = document.querySelector('#pagination-container');
                    if (newPagination && currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }

                    // Replace active filter count if applicable
                    const newFilterCount = doc.querySelector('#active-filter-count');
                    const currentFilterCount = document.querySelector('#active-filter-count');
                    if (newFilterCount && currentFilterCount) {
                        currentFilterCount.innerHTML = newFilterCount.innerHTML;
                    }

                    // Update URL bar
                    window.history.pushState({}, '', url.toString());
                })
                .catch(error => console.error('Live search error:', error))
                .finally(() => {
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                });
                
            }, 500); // 500ms debounce delay
        }
    });
});
