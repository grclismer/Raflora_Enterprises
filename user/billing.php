<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../user/login.html");
    exit();
}

// Check if user is an admin or client
$is_admin = ($_SESSION['role'] === 'admin_type');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reciept</title>
    <link rel="stylesheet" href="../assets/css/user/billing.css">
    <link rel="stylesheet" href="../assets/css/user/terms-conditions.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="../assets/js/user/billing.js"></script>
</head>
<body>
    <div class="receipt-container">
        <div class="left-section">
            <h1 class="thank-you">Thank you for your purchase!</h1>
            <div class="billing-section">
                <h3>Billing address</h3>
                <div class="billing-info">
                    <h4>Name</h4>
                    <p>John Doe</p>
                </div>
                <div class="billing-info">
                    <h4>Address</h4>
                    <p>1234 Makati st. sample, sample address<br>B2 04145, Makati City</p>
                </div>
                <div class="billing-info">
                    <h4>Phone</h4>
                    <p>+69 9123456789</p>
                </div>
                <div class="billing-info">
                    <h4>Email</h4>
                    <p>johndoe@example.com</p>
                </div>
            </div>
            <div class="terms-section">
                <div class="checkbox">
                    <input type="checkbox" checked>
                    <span>I read and agree to <a href="#" id="showPrivacyPolicy">Privacy Policy</a></span>
                </div>
                <div class="checkbox">
                    <input type="checkbox" checked>
                    <span>I read and agree to <a href="#" id="showTermsCondition">Terms and Condition</a></span>
                </div>
                <!-- kailangan i link yung bagong update kasi php na siya -->
                <button class="proceed-btn"><a href="../api/landing.php">Proceed</button></a>
                <a href="#Feedback" class="feedback-link">Feedback and evaluation</a>
            </div>
        </div>
        <div class="right-section">
            <div class="summary-header">
                <div class="logo"><img src="../assets/images/logo/raflora-logo.jpg" alt="raflora-logo"></div>
                <h3>Summary of deliverables</h3>
            </div>
            <div class="order-details">
                <div class="detail-item">
                    <div class="detail-label">Date</div>
                    <div class="detail-value">04-27-25</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Order number</div>
                    <div class="detail-value">001</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Payment method</div>
                    <div class="detail-value">Online Payment</div>
                </div>
            </div>
            <div class="items-table">
                <div class="items-header">
                    <div>Item</div>
                    <div>Unit cost</div>
                    <div>Quantity</div>
                    <div>Total cost</div>
                </div>
                <div class="item-row">
                    <div>
                        <strong>A. ENTOURAGE</strong>
                        <div class="item-details">
                            *Bridal Bouquet *Mothers<br>
                            *Grandmothers *Principal<br>
                            Sponsors *Maid/Matrons<br>
                            *Bridesmaids *Candle, Veil,<br>
                            Chord *Coin, Bible Bearers<br>
                            *Offertory Bearers *Flower<br>
                            Girl *All Male Boutonnieres
                        </div>
                    </div>
                    <div>-</div>
                    <div class="item-details">
                        1 Bouquet 2 Bouquets 2<br>
                        Bouquets 5 Bouquets 6<br>
                        Bouquets 2 Wrist Corsages<br>
                        5 Wrist Corsages 2<br>
                        Poutnnieres 1 Groom 14<br>
                        Male Entourage
                    </div>
                    <div class="item-price">
                        ₱9,500.00
                    </div>
                </div>
                <div class="item-row">
                    <div>
                        <strong>B. BALLROOM<br>ENTRANCE COLUMNS</strong>
                    </div>
                    <div>-</div>
                    <div>
                        1 Pair (Size: H-8' x D-2')
                    </div>
                    <div class="item-price">
                        ₱10,870.00
                    </div>
                </div>
                <div class="item-row">
                    <div>
                        <strong>C. RECEPTION</strong>
                        <div class="item-details">
                            16 Tables (Part of the Hotel<br>
                            Package, Upgraded to<br>
                            Long & Low); 16 Long &<br>
                            Low + 1 Additional x 16<br>
                            Tables + 16 Additional<br>
                            Long & Low *Not Part of<br>
                            Package; 4 Long & Low<br>
                            (From VIP) 16 Tables Tall<br>
                            (Upgraded) Not Part of<br>
                            (Package) 1 Tall x 16 Tables<br>
                            (Upgraded) *Cake Table<br>
                            Arrangements *Part of<br>
                            Package; 2 Tall (From VIP)<br>
                            *Couples Table (Part of<br>
                            Package) *Cake Table<br>
                            (Upgraded) *Long & Low<br>
                            (Complementary)
                        </div>
                    </div>
                    <div>-</div>
                    <div class="item-details">
                        16 Pieces (Long & Low)<br>
                        16 Pieces (Tall<br>
                        Arrangements)
                    </div>

                    <div class="item-price">
                        ₱17,450.00
                    </div>
                </div>
            </div>
            <div class="design-options">
                <i class="fa-regular fa-image"></i>
                <i class="fa-regular fa-image"></i>
                <i class="fa-regular fa-image"></i>
                <span class="preferred-design">preferred design</span>
            </div>
            <div class="total-section">
                <span>GRANDTOTAL</span>
                <span>₱39,260.00</span>
            </div>
        </div>
    </div>
    <div id="modalOverlay" class="modal-overlay"></div>
    <div id="modalContainer" class="modal-container">
        <div id="modalContent" class="modal-content">
            <button id="closeModalBtn" class="close-btn">&times;</button>
            <div id="termsandCondition" class="modal-text hidden">
                <h2>TERMS AND CONDITIONS:</h2>
                <p class="subtitle">Contractor: RAFLORA ENTERPRISES</p>
                <div class="section"><p>Contractor shall be given independence as stylist to choose colors, materials, and accessories that will be compatible with the theme and inspiration
                    <span class="highlighted">AS REQUIRED BY THE CLIENT</span>. Client accepts that flowers and materials shape, form and colors may vary from attached sketches or photo pegs. As stylist, Contractor shall be given
                    <span class="highlighted">artistic license</span> in dealing with the available materials as they see fit, adhering as close as possible to the approved presentation.</p></div>
                <div class="section"><p class="section-title">A. Meals:</p><p>Client shall provide (3) three meals and continuous supply of clean drinking water for
                    <span class="highlighted">Contractor's crew</span> during the whole duration of the installation until final conclusion. In the event that client fails to provide meals, client shall be invoiced for the provision of meals.</p></div>
                <div class="section"><p class="section-title">B. Electrician:</p><p>Raflora Enterprises shall arrange for an electrician to tap any Décor that has electrical elements to a power source compatible with the electrical requirements of the materials concerned. This is to avoid and keep in control electrical fire hazards.</p></div>
                <div class="section"><p class="section-title">C. Raflora Enterprises</p><p>shall have recourse for assistance from Event Venue for tools and equipment, (e.g., tall ladders or scaffolding) necessary for the completion of certain tasks, and should
                    <span class="highlighted">assist in providing personnel</span> who can install that which may be inaccessible under normal conditions.</p></div>
                <div class="section"><p class="section-title">D. Holding Room:</p><p>Hotel Venue, in coordination with Client, should make available a holding room where delivered materials and accessories for installation can be stored and are easily accessible. The holding room will also serve as preparation area for the finished product is installed and should be furnished with tables and chairs. This should also be protected from the elements to safeguard the integrity of the materials for installation, and shall serve as private and rest area for Crew to take their meals and completion of installation until Egress.</p></div>
                <div class="section"><p class="section-title">E. Contractor</p><p>shall not be liable for any consequential damages that may arise due to unforeseen events such as malfunction of any equipment,
                    <span class="highlighted">forces of nature (typhoon, strong winds and rain)</span> that may cause damage to installed materials and decorations, acts of war, accidents, unexpected governmental acts that may cause unforeseen traffic or any delay, and political/social unrest.</p></div>
                <div class="section"><p class="section-title">F. All goods, accessories and decorative materials from RAFLORA'S INVENTORY</p><p>from their warehouse are considered as
                    <span class="highlighted">RENTALS (Inclusive in the Proposal Costs)</span> for the duration of the event. EX: Vases, Votives, Accent Decors, Electronic Votives and Candles, Floating Battery-Operated Candles, Tassels, Faux Flowers, Avatar Lights, Gold Metal Structures, Bubble Lights & Tube Lights, etc.</p></div>
            </div>
            <div id="privacyPolicy" class="modal-text hidden">
                <h2>DISCLAIMER:</h2>
                <p class="subtitle">All presentation photos in this proposal have been gathered/generated from public design requirements of the Client. Actual installations may have variations in sizes, colors and design form but we will remain faithful to the Client's theme and requirements.</p>
                <div class="section"><p class="section-title">1.Publicly Available:</p><p>The photo pegs presented in this proposal have been gathered/generated from publicly available sources, such as websites, publications, and other open platforms. These images remain in the public domain or under their respective ownership.</p></div>
                <div class="section"><p class="section-title">2.Fresh Perspectives:</p><p>Our intention is to bring innovative and unique viewpoints to the project. While these photo pegs serve as an initial starting point, we are dedicated to creating original installations that goes beyond referenced materials. Our goal is fresh and inventive interpretations that align with your vision.</p></div>
                <div class="section"><p class="section-title">3.Fresh Interpretations:</p><p>Our primary objective is to offer innovative and distinct perspectives based on the influence of the client's Mood Board, Color Theme, etc. These photo pegs are intended to ignite creativity and provide an initial visual reference, but they do not entirely define our original content nor represents a fresh take on the project.</p></div>
                <div class="section"><p class="section-title">4.Collaboration Focus:</p><p>We aim to offer innovative and distinct perspectives. They are intended to spark creativity and set a tone for the project, but the final results may differ due to factors such as the project's evolving nature and client support. Instead, we focus final availability of seasonal flowers, accessories at any given time.</p></div>
                <div class="section"><p class="section-title">5.Open Collaboration:</p><p>We encourage an open and collaborative environment. We value our client's input, feedback, and ideas throughout the project's development, which may lead to further innovative and creative perspectives.</p></div>
                <div class="section"><p>By revieswing this proposal and the associated photo pegs, you acknowledge and accept the terms outlined in this disclaimer. If you have any inquiries, concerns, or need further clarification, please do not hesitate to reach out. We are enthusiastic about the opportunity to work together and keep the project to life with fresh and unique perspectives.</p></div>
                <div class="payment-terms">
                    <h3>CLIENT PRIORITY AND TERMS OF PAYMENT</h3>
                    <div class="payment-details">
                        <p><span class="highlighted">First-Come, First-Serve Policy:</span> Services are provided on a first-come, first-serve basis, based on the receipt of a down-payment. Raflora Enterprises reserves the right to prioritize clients, whose event occurs on the same date with other client/ clients, who have confirmed their event by submitting their down-payment first and who have applied for this priority status shall be in the last confirmed in this interim, and will not be liable from accepting another client who has provided their down payment first. Raflora Enterprises is not responsible for any potential inconvenience or impact on a client's event due to the first-come, first-serve policy.</p>
                        <div class="payment-row">
                            <span>⮚ 50 % DOWNPAYMENT UPON APPROVAL & SIGNING OF CONTRACT</span>
                        </div>
                        <div class="payment-row">
                            <span>⮚ 50 % BALANCE OF PAYMENT 30 DAYS BEFORE THE EVENT</span>
                        </div>
                        <div class="payment-row">
                            <span>RAFLORA ENTERPRISES - BIR TIN: 944-328-187-000 (NON-VAT)</span>
                        </div>
                        <div class="payment-row">
                            <span>BDO SAVINGS ACCOUNT: 0013 - 9018 - 3937</span>
                        </div>
                        <p style="margin-top: 15px;"><span class="highlighted">A. Proposal</span> is based on above-given areas and quantity. No variation of costs less than the approved and confirmed Grand Total shall be allowed for any regional and confirmation of this Contract.</p>
                        <p><span class="highlighted">B. Overdue accounts</span> are subject to interest based on prevailing bank rates from the time it becomes overdue until full payment.</p>
                        <p><span class="highlighted">C. This formal quotation</span> also serves as the formal contract of confirmation. All contents, values, rates and other particulars of this Formal Quotation is <span class="highlighted">strictly confidential and only for the perusal of the intended client.</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>