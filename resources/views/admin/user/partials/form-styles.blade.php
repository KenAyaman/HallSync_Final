<style>
.access-form-page {
    width: min(920px, 100%);
    margin: 0 auto;
    display: grid;
    gap: 18px;
}
body.role-manager:has(.access-form-page) {
    --admin-bg-top: #ede6db;
    --admin-bg-mid: #e7ded1;
    --admin-bg-bottom: #ede6db;
}
.access-form-hero, .access-form-card {
    border: 1px solid #4f4336;
    border-radius: 12px;
}
.access-form-hero {
    padding: 22px 24px;
    background: linear-gradient(115deg, #241c14, #493522);
}
.access-form-hero a {
    color: #f0c98f;
    font-size: .78rem;
    font-weight: 800;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: .08em;
}
.access-form-hero p {
    margin: 18px 0 6px;
    color: #e5b66e;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .18em;
    text-transform: uppercase;
}
.access-form-hero h1 {
    margin: 0;
    color: #fffaf3;
    font-family: 'Playfair Display', serif;
    font-size: 2.25rem;
}
.access-form-hero span {
    display: block;
    margin-top: 7px;
    color: #eadfce;
    line-height: 1.6;
}
.access-form-card {
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.10);
}
.access-form {
    padding: 20px;
}
.access-form-head {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    padding-bottom: 14px;
    border-bottom: 1px solid #e3d8ca;
}
.access-form-head h2 {
    margin: 0;
    color: #3c342d;
    font-family: 'Playfair Display', serif;
    font-size: 1.35rem;
}
.access-form-head p {
    margin: 4px 0 0;
    color: #817467;
    font-size: .82rem;
}
.access-form-head > span {
    align-self: flex-start;
    padding: 5px 8px;
    border: 1px solid #d2ae7b;
    border-radius: 999px;
    background: #f3e3cc;
    color: #68400f;
    font-size: .68rem;
    font-weight: 800;
}
.access-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-top: 16px;
}
.access-form-grid label > span {
    display: block;
    margin-bottom: 6px;
    color: #62574d;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.access-form-grid label > span em {
    margin-left: 5px;
    color: #9a8875;
    font-size: .64rem;
    font-style: normal;
    letter-spacing: .04em;
}
.access-form-grid input, .access-form-grid select {
    width: 100%;
    min-height: 40px;
    padding: 0 11px;
    border: 1px solid #d7cab9;
    border-radius: 7px;
    background: #fffdf9;
    color: #453b33;
}
.access-form-grid small {
    display: block;
    margin-top: 5px;
    color: #8f342e;
    font-size: .76rem;
}
.access-form-check {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    color: #62574d;
    font-size: .85rem;
}
.access-form-note {
    margin-top: 14px;
    padding: 11px 12px;
    border: 1px solid #d2ae7b;
    border-radius: 8px;
    background: #f3e3cc;
    color: #68400f;
    font-size: .82rem;
}
.access-form-actions {
    display: flex;
    gap: 8px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid #e3d8ca;
}
.access-form-actions a {
    text-decoration: none;
}
@media (max-width:640px) {
    .access-form-grid {
        grid-template-columns: 1fr;
    }
    .access-form-actions {
        flex-direction: column;
    }
}
</style>
