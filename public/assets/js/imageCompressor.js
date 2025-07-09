class ImageCompressor {
    constructor(options = {}) {
        // Default options
        this.options = {
            maxWidth: options.maxWidth || 1024,
            maxHeight: options.maxHeight || 1024,
            quality: options.quality || 0.7,
            mimeType: options.mimeType || 'image/jpeg'
        };
    }

    async compressImage(file) {
        return new Promise((resolve, reject) => {
            // Check if file is an image
            if (!file.type.startsWith('image/')) {
                reject(new Error('File is not an image'));
                return;
            }

            const img = new Image();
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // Create URL from file
            img.src = URL.createObjectURL(file);

            img.onload = () => {
                // Calculate new dimensions while maintaining aspect ratio
                let { width, height } = this.calculateDimensions(
                    img.width,
                    img.height
                );

                // Set canvas dimensions
                canvas.width = width;
                canvas.height = height;

                // Draw image on canvas
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to blob
                canvas.toBlob(
                    (blob) => {
                        // Clean up
                        URL.revokeObjectURL(img.src);
                        
                        // Create new file from blob with original filename
                        const compressedFile = new File(
                            [blob],
                            file.name,
                            {
                                type: this.options.mimeType,
                                lastModified: Date.now()
                            }
                        );
                        resolve(compressedFile);
                    },
                    this.options.mimeType,
                    this.options.quality
                );
            };

            img.onerror = (error) => {
                URL.revokeObjectURL(img.src);
                reject(error);
            };
        });
    }

    calculateDimensions(width, height) {
        let newWidth = width;
        let newHeight = height;

        // If image exceeds max dimensions, scale down
        if (width > this.options.maxWidth) {
            newWidth = this.options.maxWidth;
            newHeight = (height * this.options.maxWidth) / width;
        }

        if (newHeight > this.options.maxHeight) {
            newHeight = this.options.maxHeight;
            newWidth = (width * this.options.maxHeight) / height;
        }

        return { width: Math.round(newWidth), height: Math.round(newHeight) };
    }
}