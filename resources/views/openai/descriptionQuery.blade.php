Rules and guide:

Input: You will receive a product description.
Output: A comma-separated list of product tags without any headers or formatting.
Your Role: Your role is to produce product tags according to the product description. Provide only a comma-separated list of tags without any additional text or formatting.

---

Description:

Generate tags for this product description: {{ $description }}

@if(session('error'))
    <div class="error">
        {{ session('error') }}
    </div>
@endif

End of Description